# DDD Module Generation Guide (Tag Module Example)

> **Context:** This guide explains how to generate a DDD-compliant module in Laravel, using `@app-modules/tag` as the reference implementation.
> **Target Audience:** Developers and AI Agents creating new modules.

---

## 1. Directory Structure

A module must follow this strict directory layout:

```text
src/
├── Application/
│   ├── CommandHandlers/  # Business logic for writes (Create, Update, Delete)
│   ├── Commands/         # DTOs for write operations
│   ├── DTOs/             # Data Transfer Objects for reads
│   ├── Ports/            # Interfaces (Ports) for Infrastructure
│   ├── Queries/          # DTOs for read operations
│   └── QueryHandlers/    # Business logic for reads
├── Domain/
│   ├── Entities/         # Rich Domain Models (State + Behavior)
│   ├── Events/           # Facts that happened (Past Tense)
│   ├── Exceptions/       # Domain-specific errors
│   ├── Repositories/     # Interfaces for persistence (Write only)
│   └── ValueObjects/     # Immutable, self-validating objects
├── Infrastructure/
│   ├── Bus/              # Event Bus adapters
│   ├── Listeners/        # Event listeners (e.g., Cache Invalidation)
│   ├── Persistence/      # Database implementation
│   │   └── Eloquent/     # Eloquent specific (Models, Repos, Mappers)
│   ├── Providers/        # Service Providers (Wiring)
│   └── Transaction/      # Transaction management adapters
└── Presentation/
    ├── Controllers/      # HTTP Entry points
    ├── Mappers/          # HTTP Request -> Command Mappers
    ├── Policies/         # Authorization rules
    ├── Requests/         # FormRequests (Validation)
    └── Routes/           # Route definitions
```

---

## 2. Step-by-Step Implementation

### Step 1: The Domain Layer (Pure PHP)

Define the "What". No Laravel dependencies allowed here (except basics).

1.  **Value Objects:** Create `TagName`, `TagSlug`, `TagId`.
    *   *Why?* Encapsulate validation rules (e.g., `TagName::MAX_LENGTH`).
    *   *Rule:* Constructors must throw `InvalidArgumentException` on failure.
2.  **Entities:** Create `Tag`.
    *   *Why?* To hold state and behavior.
    *   *Rule:* Use `private __construct`. Expose `create()` (new) and `reconstitute()` (load).
    *   *Rule:* Use `record()` for events and `pullEvents()` to release them.
3.  **Repository Interface:** Create `TagRepository`.
    *   *Why?* To define how to save/delete without knowing about SQL.
4.  **Events:** Create `TagCreated`, `TagUpdated`.
    *   *Why?* To notify the system of changes without coupling.

### Step 2: The Application Layer (Orchestration)

Define the "How". Connects Domain to the World.

1.  **Commands/Queries:** Create simple DTOs (`CreateTagCommand`, `ListTagsQuery`).
2.  **Ports:** Define interfaces for `EventBus` and `TransactionManager`.
3.  **Handlers:** Create `CreateTagHandler`.
    *   *Flow:* Transaction -> Domain Factory -> Repository Save -> Event Bus Publish.
    *   *Rule:* Handlers must not return Eloquent models. Return `void` or simple types/DTOs.

### Step 3: The Infrastructure Layer (The Glue)

Implement the Interfaces using Laravel.

1.  **Persistence:**
    *   `TagModel` (Eloquent).
    *   `TagMapper`: Converts Entity <-> Model.
    *   `EloquentTagRepository`: Implements `TagRepository`. Catches `QueryException` and throws Domain Exceptions (`TagInUseException`).
2.  **Read Models:** `EloquentTagReader` (and `CachingTagReader` decorator).
    *   *Why?* Optimized reads returning DTOs, skipping Entity hydration overhead.
3.  **Service Provider:** Bind everything in `TagServiceProvider`.

### Step 4: The Presentation Layer (The Interface)

Handle HTTP.

1.  **Requests:** `StoreTagRequest`. Use Domain constants (`TagName::MAX_LENGTH`) for validation.
2.  **Mappers:** `CreateTagCommandMapper`. Transforms Request array -> Command Object.
3.  **Controller:** `TagController`.
    *   *Rule:* Thin. Authorize -> Validate -> Map -> Delegate to Handler -> Respond.
    *   *Error Handling:* Catch Domain Exceptions (`SlugAlreadyExistsException`) and throw Validation Exceptions or Abort.

---

## 3. Key Rules & Patterns

### 1. The "No Leak" Rule
*   **Controller** never sees **Eloquent Models**. It sees **DTOs**.
*   **Domain** never sees **Request** objects. It sees **Commands/ValueObjects**.

### 2. Transaction Management
*   Transactions are managed in **Command Handlers** via `TransactionManager`.
*   Repositories do **not** start transactions.

### 3. Event Handling
*   Entities `record()` events internally.
*   Command Handlers `pullEvents()` and `publish()` them via `EventBus` *after* persistence.

### 4. Exception Handling
*   Infrastructure (Repository) catches SQL errors (e.g., integrity violation).
*   It throws **Domain Exceptions** (`TagInUseException`).
*   Controller catches Domain Exception and decides the HTTP response.

### 5. Caching
*   Use the **Decorator Pattern**. `CachingTagReader` wraps `EloquentTagReader`.
*   Invalidate cache using **Event Listeners** (`TagCacheInvalidator` listens to `TagUpdated`).

---

## 4. Common Pitfalls (Don't do this)

*   ❌ **Don't** return `Tag` Entity from a Query Handler. Return `TagDTO`.
*   ❌ **Don't** use `DB::transaction` directly in Handlers. Use the Port.
*   ❌ **Don't** put validation logic in Controllers. Put it in FormRequests (structure) and ValueObjects (rules).
*   ❌ **Don't** let Domain Objects access the Database.

---

## 5. Generator Command Logic

When updating the generator command, ensure it creates:
1.  **All 4 Layers** folders.
2.  **Standard Ports** (`EventBus`, `TransactionManager`) if missing.
3.  **Mapper** for Entity <-> Model.
4.  **Read Model** interface and implementation.