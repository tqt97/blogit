# 🏷️ Tag Module: The Ultimate Architectural Deep Dive

This document serves as the definitive guide to the `Tag` module. It explores the intricate class relationships, dependency management, data lifecycles, and the underlying "Why" behind this DDD (Domain-Driven Design) implementation.

---

## 🏛️ 1. The Core Philosophy: "Domain is King"

In this architecture, the **Domain** is the center of the universe. All other layers (Presentation, Application, Infrastructure) exist only to serve or support the Domain.

### 🧩 Architectural Overview

```mermaid
graph TD
    subgraph Presentation
        Controller[TagController]
        Request[FormRequest]
        PresMapper[Command/Query Mapper]
    end

    subgraph Application
        Command[Command DTO]
        Handler[Command/Query Handler]
        AppDTO[Output DTO]
    end

    subgraph Domain
        Entity[Tag Entity]
        VO[Value Objects: TagId, TagSlug...]
        RepoInterface[TagRepository Interface]
        Events[Domain Events]
    end

    subgraph Infrastructure
        RepoImpl[EloquentTagRepository]
        Eloquent[Eloquent Model]
        InfraMapper[TagMapper]
        Cache[Caching Reader]
    end

    Presentation --> Application
    Application --> Domain
    Infrastructure -- implements --> RepoInterface
    Infrastructure --> Domain
```

### 📏 The Inward Dependency Rule

* **Presentation ➡️ Application ➡️ Domain**
* **Infrastructure ➡️ Application / Domain** (Implementing interfaces)
* **Domain ➡️ Nothing**

---

## 🔌 2. Class Communication & Dependency Injection (DI)

We strictly avoid the `new` keyword for any class that contains logic. Instead, we use **Constructor Injection**.

### 🛠️ The Service Container (The Electrician)

The `TagServiceProvider` is the "Glue". It tells Laravel how to build complex objects.

* **Binding Interfaces**: When a Handler asks for `TagRepository`, Laravel looks at the Service Provider and sees it should provide `EloquentTagRepository`.
* **Automatic Resolution**: If `EloquentTagRepository` needs a `TagMapper`, Laravel automatically creates the mapper and injects it.

---

## 🔄 3. Full CRUD Flows

### 🆕 CREATE Flow

```mermaid
sequenceDiagram
    participant U as User
    participant C as TagController
    participant H as CreateTagHandler
    participant E as Tag Entity
    participant R as EloquentTagRepository
    participant L as CacheListener

    U->>C: POST /tags (name, slug)
    C->>H: handle(CreateTagCommand)
    H->>E: Tag::create(TagName, TagSlug)
    H->>R: save(tag)
    R->>R: DB Transaction
    R->>L: Dispatch TagCreated Event
    L->>L: Clear List Cache
    C->>U: Redirect to Index
```

### 🔍 READ Flow (Caching)

```mermaid
sequenceDiagram
    participant U as User
    participant C as TagController
    participant H as ListTagsHandler
    participant CR as CachingTagReader
    participant ER as EloquentTagReader

    U->>C: GET /tags
    C->>H: handle(ListTagsQuery)
    H->>CR: paginate(search, sort)
    alt is cached
        CR-->>H: Return Cached DTOs
    else not cached
        CR->>ER: paginate()
        ER-->>CR: Return Eloquent Results
        CR->>CR: Store in Cache
        CR-->>H: Return DTOs
    end
    H-->>C: LengthAwarePaginator[TagDTO]
    C->>U: Render Inertia Page
```

### 📝 UPDATE Flow

```mermaid
sequenceDiagram
    participant C as TagController
    participant H as UpdateTagHandler
    participant R as EloquentTagRepository
    participant E as Tag Entity

    C->>H: handle(UpdateTagCommand)
    H->>R: getById(TagId)
    R-->>H: Tag Entity
    H->>E: rename(TagName) / changeSlug(TagSlug)
    H->>R: save(tag)
    R->>R: DB Commit
    R->>C: Updated Entity
    C->>C: Flash Message & Back
```

### 🗑️ DELETE Flow

```mermaid
sequenceDiagram
    participant C as TagController
    participant H as DeleteTagHandler
    participant R as EloquentTagRepository
    participant L as CacheListener

    C->>H: handle(DeleteTagCommand)
    H->>R: delete(TagId)
    R->>R: DB Delete
    R->>L: Dispatch TagDeleted Event
    L->>L: Forget Find Cache & List Cache
    C->>C: Back with Success
```

---

## 💎 4. Key Design Patterns Used

### 1️⃣ Command-Query Separation (CQS) ⚔️

We separate "Writing" from "Reading".

* **Write Path**: Use Handlers, Commands, and Entities. Focus is on business rules and data integrity.
* **Read Path**: Use QueryHandlers and DTOs. Focus is on speed, pagination, and caching.

### 2️⃣ The Decorator Pattern (Caching) 🎀

Look at `CachingTagReader`. It wraps `EloquentTagReader`.

* The `CachingTagReader` handles the caching logic.
* The `EloquentTagReader` handles the raw DB queries.
* The Service Provider decides how to "nest" them.

### 3️⃣ Data Mapper Pattern 🗺️

`TagMapper` keeps the Domain Entity and the Database Model separate.

* **Model**: Talks to Laravel/DB.
* **Entity**: Pure PHP, talks business rules.
* **Benefit**: You can change your DB schema without breaking your Domain.

---

## 🏗️ 5. Implementation "Glue": The Service Provider

| Method | 🛠️ Role |
| :--- | :--- |
| `register()` | Binds interfaces to implementations. Sets up **Singletons**. |
| `boot()` | Sets up framework features: Routes, Policies, Event Listeners. |
| `mergeConfigFrom` | Merges module defaults with the main app config. |
| `runningInConsole` | Ensures "setup" logic only runs in CLI mode. |

---

## ⚖️ 6. Advantages vs. Disadvantages

### ✅ The Pros

* **Testability**: Test logic without a database.
* **Isolation**: Bugs in one module can't "bleed" into others.
* **Cognitive Clarity**: You always know where the "rules" live.
* **Zero Side Effects**: Value Objects prevent "dirty" data.

### ❌ The Cons

* **File Explosion**: A simple CRUD requires ~15 files.
* **Complexity**: Higher learning curve for new developers.
* **Speed**: Initial development is slower than "Standard Laravel".

---

## 📋 7. Summary of Class Roles

* 🚀 **Controller**: Entry/Exit.
* 🛡️ **Request**: Validation.
* 🗺️ **Mapper**: Translation.
* 📦 **Command/Query**: Intention.
* ⚙️ **Handler**: Orchestrator.
* 🌳 **Entity**: Business Logic.
* 💎 **Value Object**: Integrity.
* 💾 **Repository**: Persistence.
* 📖 **Reader**: Retrieval.
* 🔔 **Listener**: Side Effects.
