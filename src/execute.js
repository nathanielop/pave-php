import isArray from './is-array.js';
import isFunction from './is-function.js';
import isObject from './is-object.js';
import PaveError from './pave-error.js';
import validateArgs from './validate-args.js';

const execute = async ({
  context,
  obj,
  path = [],
  query,
  schema,
  type,
  value
}) => {
  const fail = (code, extra) => {
    throw new PaveError(code, {
      context,
      obj,
      path,
      query,
      schema,
      type,
      value,
      ...extra
    });
  };

  let isNullable = false;
  let isOptional = false;
  do {
    if (isFunction(value)) value = await value();
    else if (type == null) {
      if (value != null) return value;

      if (!isOptional && isNullable) return null;

      if (value === undefined && !isOptional) fail('expectedRequired');

      if (value === null && !isNullable) fail('expectedNonNull');

      return value;
    } else if (!isObject(type)) {
      if (schema[type]) {
        obj = null;
        type = schema[type];
      } else fail('unknownType');
    } else if (value === undefined && type.defaultValue !== undefined) {
      value = type.defaultValue;
    } else if (type.optional) {
      type = type.optional;
      isOptional = true;
    } else if (type.nullable) {
      type = type.nullable;
      isNullable = true;
    } else if (obj == null && value == null) type = null;
    else if (type.arrayOf) {
      if (!isArray(value)) fail('expectedArray');

      const { minLength, maxLength } = type;
      if (minLength != null && value.length < minLength) {
        fail('expectedArrayMinLength');
      }

      if (maxLength != null && value.length > maxLength) {
        fail('expectedArrayMaxLength');
      }

      return Promise.all(
        value.map((value, i) =>
          execute({
            context,
            obj,
            path: path.concat(i),
            query,
            schema,
            type: type.arrayOf,
            value
          })
        )
      );
    } else if (type.oneOf) type = type.resolveType(value);
    else if (type.fields) {
      const merged = {};
      const onKey = `_on_${type.name}`;
      for (const key in query) {
        if (key === onKey) Object.assign(merged, query[key]);
        else if (!key.startsWith('_on_')) merged[key] = query[key];
      }
      return Object.fromEntries(
        await Promise.all(
          Object.entries(merged).map(async ([alias, query]) => {
            const { _field, ..._query } = query;
            const field = _field || alias;
            let _type = type.fields[field];
            if (!_type) {
              if (field === '_type') {
                _type = { type: { nullable: {} }, resolve: type.name };
              } else fail('unknownField', { alias, field });
            }

            return [
              alias,
              await execute({
                context,
                obj: value,
                path: path.concat(alias),
                query: _query,
                schema,
                type: _type,
                value: value[field]
              })
            ];
          })
        )
      );
    } else {
      const { _args, ..._query } = query;
      let _value = 'resolve' in type ? type.resolve : value;
      if (isFunction(_value)) {
        _value = await _value({
          args: validateArgs({
            args: _args,
            context,
            path: path.concat('_args'),
            query,
            schema,
            type
          }),
          context,
          obj,
          path,
          query,
          schema,
          type,
          value
        });
      }

      if (type.typeArgs) _query._args = type.typeArgs;
      query = _query;
      type = type.type;
      value = _value;
    }
  } while (true);
};

export default execute;
