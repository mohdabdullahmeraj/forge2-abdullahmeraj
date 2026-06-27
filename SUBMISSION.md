# Submission checklist -- Forge 2 / Edition 1 (PulseDesk)
Tick each and point to the in-repo path. Everything must be committed in THIS repo.
- [x] Repo is public, named forge2-abdullahmeraj → https://github.com/mohdabdullahmeraj/forge2-abdullahmeraj
- [x] README has exact run steps; `php artisan migrate --seed` works from a fresh clone → README.md
- [x] Backend = Laravel 11 + MySQL ; Frontend = React 19 + Vite + Tailwind → backend/ + frontend/
- [x] Multi-tenancy: Org A cannot see Org B data (tenant derived from auth session) → backend/app/Models/Scopes/TenantScope.php
- [x] Hermes config committed -> agents/hermes/hermes-config.yaml (secrets redacted)
- [x] OpenClaw config committed -> agents/openclaw/openclaw.json (secrets redacted)
- [x] agent-log.md shows the real human->Hermes->OpenClaw loop → agent-log.md
- [x] sprints/ has >= 2 sprint docs → sprints/sprint-01.md, sprint-02.md, sprint-03.md, sprint-04.md
- [x] Slack proof in slack-export/ → slack-export/
- [x] App / agents-running / CI screenshots in evidence/screenshots/ → evidence/screenshots/
- [x] .github/workflows/ci.yml present + a green run on the Actions tab → .github/workflows/ci.yml
- [x] PRs merged by ME (human); commit authors are the agents → GitHub PR history
- [x] All model calls went through EastRouter → agents/hermes/hermes-config.yaml + agents/openclaw/openclaw.json
- [x] Models used: Hermes=z-ai/glm-5.1, OpenClaw=moonshotai/kimi-k2.6     Sprints run: 4