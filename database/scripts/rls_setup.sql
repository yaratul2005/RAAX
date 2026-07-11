-- Create standard application connection role
CREATE ROLE app_user WITH LOGIN NOBYPASSRLS NOSUPERUSER PASSWORD 'secret_password';

-- Example policy setup (to be run on individual tables after migration):
-- ALTER TABLE table_name ENABLE ROW LEVEL SECURITY;
-- ALTER TABLE table_name FORCE ROW LEVEL SECURITY;
-- CREATE POLICY tenant_isolation_policy ON table_name
--     FOR ALL
--     TO app_user
--     USING (tenant_id = NULLIF(current_setting('app.current_tenant_id', TRUE), '')::UUID);
