# Sprint 02 — Tickets Core (model · TenantScope · CRUD API)

Goal: Build the ticket entity with multi-tenant enforcement via a global TenantScope, plus a full REST CRUD API with filtering and search. Every query auto-scopes by the authenticated user's organization_id — no client-supplied org IDs.
Models: Hermes=z-ai/glm-5.1, OpenClaw=moonshotai/kimi-k2.6

## Issues
- [x] #4 Tickets model + migration + TenantScope global scope + seeder (12 tickets across 2 orgs)
- [x] #5 Full Tickets CRUD API (list with filters/search, create, show, update, delete) — multi-tenancy in every method

## Outcome
- Shipped: Ticket model with TicketStatus/Priority enums + TenantScope auto-filtering; TicketController (index with status/priority/assignee/search filters, store, show, update, destroy); TicketRequest validation; TicketFactory; DatabaseSeeder updated with Globex org + 12 tickets (8 Acme / 4 Globex)
- Slipped / moved to next sprint: none
- PRs: #5 #6 (merged by me)
