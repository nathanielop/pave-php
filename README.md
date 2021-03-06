# Pave
Paving the way to better state management, in PHP.

## Why?

Pave is a data layer inspired by [Relay], [GraphQL], [Falcor] and [Om (next)].
Pave attempts to take the best pieces of each (subjectively) and expose a simple
API that makes managing app state much easier.

## Goals

- **Performance**<br>
  The core of any data layer will have many hot paths and should run
  efficiently.

- **Flexible Schema**<br>
  There is no requirement that your data is strictly typed,
  but you can add constraints on inputs and outputs as you see fit.

- **POJO**<br>
  All data is represented as JSON-friendly Plain Ol' JavaScript
  Objects so there is no need to worry about how to serialize `X` and how to
  deserialize `Y`. There are no classes in Pave.

- **Multiple Remotes**<br>
  Create a client side schema to use Pave with an existing REST API, implement a
  Pave schema on the server or mix and match. Allowing multiple remotes both on
  the client and server makes integrating Pave into an existing project
  manageable.

- **Immutable**<br>
  The cache accepts updates without mutating previous states. This makes
  history tracking trivial and prohibits unexpected mutations.

- **Small**<br>
  ![](https://badgen.net/bundlephobia/minzip/pave)

## Install

```bash
npm install pave
```

[Falcor]: https://github.com/netflix/falcor
[GraphQL]: https://github.com/facebook/graphql
[Om (next)]: https://github.com/omcljs/om
[Relay]: https://github.com/facebook/relay
