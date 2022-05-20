6.0.0
  - Started changelog
  - `RightsStore` driver is replaced by entire `AuthDriver`
  - Removed paged lists but pagers that should be used for creating paged lists
  - Deprecated `PagerModel::countPageLimit()` and `PagerModel::getStartMaterialIndex()`. Use `getLimit` and `getOffset` methods instead.
  - Removed `IdentityPagedList`. Use `PagerModel` directly when building an identity paged list.