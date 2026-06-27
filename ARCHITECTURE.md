# Architecture -- PulseDesk

## Multi-tenancy approach

Every record (Organization, User, Ticket, Comment) is scoped to an `organization_id`. The tenant is **always derived from the authenticated user** (`Auth::user()->organization_id`) — never from a client-supplied ID.

- **TenantScope** (`app/Models/Scopes/TenantScope.php`) is a global Eloquent scope applied to `Ticket` and `Comment` models. It automatically appends `WHERE organization_id = ?` to all queries using the current authenticated user's organization.
- `Ticket::withoutTenantScope()` and `Comment::withoutTenantScope()` allow bypassing the scope for cross-tenant operations (e.g., seeding).
- Controllers never accept `organization_id` from client input. On `store`, the `organization_id` is set from `Auth::user()->organization_id`.

## Data model

```
Organization
├── hasMany User
├── hasMany Ticket (via TenantScope)
└── hasMany Comment (via TenantScope)

User
├── belongsTo Organization
├── hasMany Ticket (as requester)
├── hasMany Ticket (as assignee)
└── hasMany Comment (as author)

Ticket
├── belongsTo Organization
├── belongsTo User (requester)
├── belongsTo User (assignee) — nullable
└── hasMany Comment

Comment
├── belongsTo Ticket
├── belongsTo User (author)
├── belongsTo Organization
└── belongsTo Comment (parent) — nullable, self-referencing for threading
└── hasMany Comment (children)
```

## Enums

| Enum | Values |
|------|--------|
| Role | Admin, Agent, Customer |
| TicketStatus | Open, InProgress, Resolved, Closed |
| Priority | Low, Medium, High, Urgent |
| CommentType | Public, Internal |

## API routes (routes/api.php)

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| POST | /api/register | Public | Create org + admin user, returns Sanctum token |
| POST | /api/login | Public | Email + password, returns token + user |
| POST | /api/logout | Sanctum | Delete current token |
| GET | /api/user | Sanctum | Return authenticated user with org |
| GET | /api/tickets | Sanctum | List tickets (TenantScope auto-filters). Query params: status, priority, assignee_id, search |
| POST | /api/tickets | Sanctum | Create ticket (org_id from Auth::user()) |
| GET | /api/tickets/{ticket} | Sanctum | Show ticket (TenantScope ensures access) |
| PATCH | /api/tickets/{ticket} | Sanctum | Update ticket (subject, description, status, priority, assignee_id) |
| DELETE | /api/tickets/{ticket} | Sanctum | Delete ticket |
| GET | /api/tickets/{ticket}/comments | Sanctum | List comments. Customers see only `public`; agents/admins see all |
| POST | /api/tickets/{ticket}/comments | Sanctum | Add comment. Customers blocked from `internal` with 403 |

## Key decisions

- **TenantScope as global scope** rather than middleware — keeps controllers clean and prevents accidental tenant leakage.
- **Role-based comment visibility** — customers cannot see or create internal notes, enforced in controller.
- **Threaded comments** via self-referencing `parent_id` on Comment model.
- **Sanctum token auth** for API, with Bearer token stored in localStorage on the React frontend.
- **React Router** for SPA navigation, Axios interceptors for automatic token attachment.
- **Tailwind CSS** for all styling — no custom CSS beyond basic index.css imports.
