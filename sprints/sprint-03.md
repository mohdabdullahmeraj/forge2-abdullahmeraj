# Sprint 03 — Comments + React Auth (threaded replies · frontend auth)

Goal: Add threaded comments to tickets with public/internal visibility (internal notes visible to agents/admins only), and build the React frontend authentication layer — login/register pages, Axios bearer-token client, protected routes, and a basic app shell. After this sprint, a user can authenticate through the browser and every ticket has a comment thread.
Models: Hermes=z-ai/glm-5.1, OpenClaw=moonshotai/kimi-k2.6

## Issues
- [x] #6 Comments model + migration + API (threaded replies, public/internal visibility)
- [x] #7 React frontend auth (login, register, Axios bearer token, protected routes, layout)

## Outcome
- Shipped: Comment model + CommentType enum (public/internal) + migration (parent_id self-ref for threaded replies); CommentController (list, store, reply) with TenantScope; CommentRequest validation; CommentFactory; API routes + Feature tests. React auth layer: Login + Register pages, Axios bearer-token client (lib/api.js), ProtectedRoute, Layout app shell, Dashboard, Vite dev-proxy config.
- Slipped / moved to next sprint: none
- PRs: #8 #10 (merged by me)
