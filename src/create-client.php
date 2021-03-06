<?php

namespace Pave;

use cacheExecute;
use injectType;
use isEqual;
use mergeCaches;
use normalize;

class CreateClient {
    export default ({ cache, execute, getKey } = {}) => {
        const watchers = new Set();
      
        const client = {
          cache: cache ?? {},
      
          cacheExecute: ({ key, query }) =>
            cacheExecute({ cache: client.cache, key, query: injectType(query) }),
      
          cacheUpdate: ({ data }) => {
            const prev = client.cache;
            client.cache = mergeCaches(client.cache, data);
            if (client.cache === prev) return client;
      
            watchers.forEach(watcher => {
              const { data, onChange, query } = watcher;
              if (!query) return onChange(client.cache);
      
              const newData = client.cacheExecute({ query });
              if (!isEqual(data, newData)) onChange((watcher.data = newData));
            });
            return client;
          },
      
          execute: async ({ query, ...args }) => {
            query = injectType(query);
            const data = await execute({ query, ...args });
            client.update({ data, query });
            return data;
          },
      
          update: ({ data, query }) => {
            query = injectType(query);
            data = normalize({ data, getKey, query });
            client.cacheUpdate({ data });
          },
      
          watch: ({ onChange, query }) => {
            query = query && injectType(query);
            const data = query && client.cacheExecute({ query });
            const watcher = { data, onChange, query };
            watchers.add(watcher);
            return { data, unwatch: () => watchers.delete(watcher) };
          }
        };
      
        return client;
      };
}
