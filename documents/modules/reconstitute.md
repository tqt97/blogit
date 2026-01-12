# Notes

---

## 1. The `reconstitute` function is a standard `Domain-Driven Design (DDD)` pattern used to restore an existing entity from persistence (the database)

Here is why it exists and how it differs from create:

### Separation of Lifecycle

* **`create()`**: Used when a **new Tag** is being born into your system for the first time.
  * Result: It records a `TagCreated` domain event.
  * State: It usually doesn't have an `ID` yet (until saved).
* **`reconstitute()`**: Used when loading an old Tag that already exists in the database.
  * Result: It simply restores the object's state (ID, name, slug, timestamps) without recording any events. You don't want to fire a
         `TagCreated` event every time you load a tag from the database to display it.

### Encapsulation

The `__construct` method is private to strictly control how Tag objects are created. This forces code to use the specific factory methods:

* Use `Tag::create(...)` for business logic (**new tags**).
* Use `Tag::reconstitute(...)` for infrastructure logic (**loading tags**).

### Usage Example

It is primarily used by your Mapper (TagMapper.php) when converting a Database Model into a Domain Entity:

```php
    1 // In TagMapper.php
    2 public function toEntity(TagModel $model): Tag
    3 {
    4     // We are loading from DB, so we use reconstitute
    5     return Tag::reconstitute(
    6         id: new TagId((int) $model->id),
    7         name: new TagName((string) $model->name),
    8         // ...
    9     );
   10 }
```

---
