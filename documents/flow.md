# Flow

PostModule(App) ──> TagModule(Application Port) <── Presentation(Http)
                             │
                             └── Infrastructure(Eloquent Adapter)


Modules/Tag/src
├── Domain
│   ├── Entities/Tag.php
│   ├── ValueObjects/TagId.php
│   ├── ValueObjects/TagName.php
│   ├── ValueObjects/TagSlug.php
│   ├── Repositories/TagRepository.php
│   └── Rules/UniqueTagSlugRule.php
├── Application
│   ├── Commands/CreateTagCommand.php
│   ├── Commands/UpdateTagCommand.php
│   ├── Commands/DeleteTagCommand.php
│   ├── CommandHandlers/CreateTagHandler.php
│   ├── CommandHandlers/UpdateTagHandler.php
│   ├── CommandHandlers/DeleteTagHandler.php
│   ├── Queries/ListTagsQuery.php
│   ├── Queries/ShowTagQuery.php
│   ├── QueryHandlers/ListTagsHandler.php
│   ├── QueryHandlers/ShowTagHandler.php
│   └── DTOs/TagData.php
├── Infrastructure
│   ├── Persistence/Eloquent/Models/TagModel.php
│   ├── Persistence/Eloquent/Mappers/TagMapper.php
│   ├── Persistence/Eloquent/Repositories/EloquentTagRepository.php
│   └── Providers/TagServiceProvider.php
└── Presentation
    ├── routes.php
    ├── Controllers/Admin/TagController.php
    └── Requests/StoreTagRequest.php
    └── Requests/UpdateTagRequest.php
