# Task: Review Code for Architectural & Multi-Tenant Compliance

Please review the current code changes and verify:

1. Are there any direct imports of Eloquent models or services across modules? (Flag as warning if detected).
2. If new database tables are created, is PostgreSQL RLS enabled and configured correctly with the `tenant_isolation_policy`?
3. Are there any instances of float or decimal variables used to represent financial values? (Enforce integer cents instead).
4. Do all custom HTTP validation requests extend `App\Http\Requests\BaseRequest`?
