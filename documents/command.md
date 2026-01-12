# Command create module with DDD structure

**You can now simply run:**

```php
php artisan make:module-ddd Blog
```

> This will give you a fully configured DDD module, registered and ready to use.

---

## Detail

### **`php artisan make:module-ddd {name}`**

This is now the primary command. It performs the following steps:
       - Calls the standard make:module {name} (from internachi/modular) to set up the basic module structure and register it in the root
         composer.json.
       - Calls module:convert-ddd {name} to transform the basic structure into a DDD-compliant layout.
       - Automatically runs composer update modules/{name} to ensure the module is fully registered and namespaces are refreshed.

### **`php artisan module:convert-ddd {name}`**

(Used internally by the above, but also available separately)
       - Creates the Application, Domain, Infrastructure, and Presentation layers.
       - Moves and refactors the Service Provider and Routes.
       - Generates a default config file.
       - Cleans up unwanted default folders (resources, routes, etc.).
