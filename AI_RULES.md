# AI Agent: Rules of Engagement & Permissions

To maintain project integrity and the user's trust, the AI assistant must follow these rules regarding autonomous actions vs. consultation.

## ✅ Autonomous (Go Ahead)
The AI can perform these actions without explicit permission for every step:
1. **Styling Improvements**: Adjusting margins, padding, colors (within the established brand palette), and adding micro-animations/hover effects.
2. **Refactoring**: Cleaning up redundant code, improving function readability, and naming conventions (following PSR-12 and Vue Best Practices).
3. **Bug Fixes**: Fixing syntax errors, logical bugs, and UI glitches reported by the user.
4. **Boilerplate**: Creating standard CRUD components (Controllers, Models, Requests, Vue Pages) once an established pattern exists in the project.
5. **Testing**: Adding Unit or PEST tests for existing logic.
6. **Documentation**: Updating READMEs or adding JSDoc/PHPDoc comments to code.

## ⚠️ Consultative (Ask Before Doing)
The AI **MUST** seek approval via `notify_user` before:
1. **Destructive Database Changes**:
   - Dropping tables or columns.
   - Deleting or overwriting significant amounts of production data.
   - Modifying established migrations after they have been pushed.
2. **Core Logic Changes**:
   - Changing how financial profitability is calculated.
   - Modifying the core "Client Journey" flow.
   - Altering the Role/Permission hierarchy.
3. **Structural Design Changes**:
   - Introducing a new CSS framework (e.g., trying to add Bootstrap or Tailwind v4).
   - Changing the main Layout structure (already established as Full-width AppBar + Sidebar).
   - Changing the core brand identity (Purple `#7220fe`).
4. **Security & Auth**:
   - Modifying Login/Register flows.
   - Changing encryption or hashing methods.
5. **External Integrations**:
   - Setting up production API keys (Stripe, Google Cloud, Mailgun).
   - Making external HTTP requests that might incur costs.

## 🛑 Prohibited
- Never disable security middlewares (`auth`, `verified`) without a temporary debugging reason and explicit user consent.
- Never hardcode secrets or credentials into the codebase (Always use `.env`).
- Never perform "Silent" destructive actions; if a file must be deleted, explain why first.
