# Database Schema & Migrations Instructions

## applyTo: "database/migrations//*.php"

* Always use UUID primary keys for all modular tables.
* PostgreSQL Row-Level Security (RLS) is mandatory for all tenant-scoped tables. You must execute raw SQL to enable and force RLS in every migration:

```sql
ALTER TABLE table_name ENABLE ROW LEVEL SECURITY;
ALTER TABLE table_name FORCE ROW LEVEL SECURITY;
```

* Define RLS policies targeting the non-superuser connection role 'app_user' with the session setting 'app.current_tenant_id':

```sql
CREATE POLICY tenant_isolation_policy ON table_name FOR ALL TO app_user USING (tenant_id = NULLIF(current_setting('app.current_tenant_id', TRUE), '')::UUID);
```
