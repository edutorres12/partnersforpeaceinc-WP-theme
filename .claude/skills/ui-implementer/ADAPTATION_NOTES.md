# Adaptation notes for this project

The upstream `ui-implementer` skill (from mcpmarket.com) assumes:

- React 19 + TypeScript + Tailwind 4 stack
- `frontend:ui-developer`, `frontend:ui-developer-codex`, `frontend:designer` subagent types installed
- Figma MCP and Chrome DevTools MCP available
- A locally-running preview URL (Vite/Next/CRA) that the agent can navigate

**This is a WordPress theme** (PHP + Tailwind 3 + Gutenberg blocks). The skill's full workflow won't run as-is because:

1. The `frontend:*` subagents aren't installed in this Claude Code session
2. There is no Figma MCP or Chrome DevTools MCP available
3. There is no local dev preview URL — the live site is on Hostinger and the sandbox has no outbound HTTP
4. `npx tsc --noEmit` doesn't apply (no TypeScript)

## Practical equivalent in this repo

When you want a pixel-perfect pass:

1. Provide the design reference: a PDF page or a screenshot. Upload the file so Claude reads it directly.
2. Provide the live URL screenshot manually (Chrome DevTools → "Capture full size screenshot") — the model cannot reach the URL.
3. Claude compares both, lists gaps, ships a PR.
4. After merge + deploy, send another screenshot for the next iteration.

This is exactly the loop we've been running. The skill formalizes it; the dependencies it needs aren't here yet.

## To activate the upstream skill end-to-end

If you want the full automated loop (designer subagent comparing screenshots, codex agent for hard fixes):

1. Install the `frontend:ui-developer`, `frontend:ui-developer-codex`, and `frontend:designer` subagents at `~/.claude/agents/` (or via plugin).
2. Install Chrome DevTools MCP server in your Claude Code config so the model can drive a real browser.
3. (Optional) Install Figma MCP if you ever switch to Figma references.
4. Re-open Claude Code so all of this loads.

Until then, treat this SKILL.md as documentation of the desired workflow.
