# Sprint 01 — Foundation (tenancy · users · auth)

Goal: Establish the multi-tenant data foundation and a working Sanctum token auth API. After this sprint, a user can register, log in, and receive a bearer token scoped to an organization and a role (admin | agent | customer).
Models: Hermes=z-ai/glm-5.1, OpenClaw=moonshotai/kimi-k2.6

## Issues
- [x] #1 Organizations model + migration + factory
- [x] #2 Users with roles (org-scoped) + HasApiTokens + seeder
- [x] #3 Sanctum auth API (register / login / logout)

## Outcome
- Shipped: Organization model/migration/factory; User with organization_id + Role enum + Sanctum HasApiTokens; AuthController (register/login/logout); api.php routes; DatabaseSeeder (Acme org + 3 demo users admin/agent/customer@acme.test)
- Slipped / moved to next sprint: none
- PRs: #1 #2 #3 (merged by me)
