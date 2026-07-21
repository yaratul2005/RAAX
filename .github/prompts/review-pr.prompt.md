# Task: Review Code for Architectural & Multi-Tenant Compliance

## name: review-pr
## description: Perform a strict architectural and security code review on the changes

Please review the current code changes and verify:

1. **Cross-Module Imports**: Are there any direct imports of Eloquent models or services across modules? (Flag as warning if detected).

2. **Database Security**: If new database tables are created, is PostgreSQL RLS enabled and configured correctly with the 'tenant_isolation_policy'?

3. **Financial Precision**: Are there any instances of float or decimal variables used to represent financial values? (Enforce integer cents instead).

4. **Request Validation**: Do all custom HTTP validation requests extend `App\Http\Requests\BaseRequest`?
