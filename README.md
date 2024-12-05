# PHP Framework Documentation

A modern PHP framework featuring attributes-based routing, multiple view engines, migrations, models, and more.

## Table of Contents
- [Installation](#installation)
- [Features](#features)
- [Usage](#usage)
    - [Application Setup](#application-setup)
        - [Web Application](#web-application)
        - [Console Application](#console-application)
    - [Directory Structure](#directory-structure)
    - [Console Commands](#console-commands)
        - [Built-in Commands](#built-in-commands)
        - [Interactive Debugging](#interactive-debugging)
    - [Routing & Controllers](#routing--controllers)
    - [Database](#database)
        - [Migrations](#migrations)
        - [Models](#models)
        - [Relationships](#relationships)
        - [Seeders & Factories](#seeders--factories)
    - [Forms & Validation](#forms--validation)
    - [Events](#events)
    - [WebSocket](#websocket)
    - [Caching](#caching)
    - [Logging](#logging)
        - [Configuration](#configuration)
        - [Handlers](#handlers)
        - [Log Levels](#log-levels)

## Features
- Attribute-based routing system
- Multiple view engine support (Blade, Twig, Inertia)
- Database migrations and models
- Eloquent-style relationships using attributes
- Form validation using attributes
- Event system
- WebSocket support
- Redis/File-based caching
- Factory pattern for testing
- Middleware support
- Interactive console debugging
- PSR-3 compatible logging system
    - Database and file handlers
    - Multiple channels support
    - Configurable formatters
- Command-line interface
    - Built-in CRUD generators
    - Database management
    - WebSocket server control
    - Interactive debugging tools
- Error handling
    - Web and console error handlers
    - Detailed backtraces
    - Interactive debugging console

## Installation
[Installation instructions to be added]

## Usage

### Application Setup

#### Directory Structure
```
your-project/
├── app/
│   ├── Console/
│   │   └── Commands/        # Custom console commands
│   ├── Controllers/         # HTTP controllers
│   └── Database/
│       ├── Migrations/      # Database migrations
│       ├── Models/          # Database models
│       ├── Factories/       # Model factories
│       └── Seeders/        # Database seeders
├── bootstrap/
│   └── app.php             # Application bootstrap file
├── console/
│   └── bin                 # Console application entry point
├── public/
│   └── index.php          # Web application entry point
└── routes/
    └── web.php            # Web routes definition
```

#### Bootstrap (bootstrap/app.php)
Initialize the application instance:

```php
<?php

require_once '../vendor/autoload.php';
require_once '../helpers/functions.php';

use DJWeb\Framework\Web\Application;

$app = Application::getInstance();
$app->bind('base_path', dirname(__DIR__));
```

#### Entry Point (public/index.php)
Configure error handling and start the application:

```php
<?php

use DJWeb\Framework\ErrorHandling\Backtrace;
use DJWeb\Framework\ErrorHandling\Handlers\WebHandler;
use DJWeb\Framework\ErrorHandling\Renderers\WebRenderer;
use DJWeb\Framework\Web\Application;

require_once '../bootstrap/app.php';

$errorHandler = new WebHandler(
    new WebRenderer(
        debug: true,
        backtrace: new Backtrace()
    ),
);

$app = Application::getInstance();
$app->session->start();
$app->loadRoutes(
    controllerNamespace: '\\App\\Controllers\\',
    controllerDirectory: __DIR__ . '/../app/Controllers/'
);
$app->withRoutes(require_once '../routes/web.php');
echo $app->handle()->getBody()->getContents();

### Console Commands
Create custom console commands using attributes:
```

#### Console Application (console/bin)
Set up the console application for running commands:

```php
<?php

use DJWeb\Framework\Console\Application;
use DJWeb\Framework\Console\Utils\CommandNamespace;
use DJWeb\Framework\ErrorHandling\Handlers\ConsoleHandler;
use DJWeb\Framework\ErrorHandling\Renderers\Partials\ConsoleRendererFactory;

require_once 'vendor/autoload.php';
require_once 'helpers/functions.php';

$app = Application::getInstance();

// Configure error handling
$handler = new ConsoleHandler(new ConsoleRendererFactory($app)->create());
$handler->register();

// Set up application paths
$app->bind('base_path', dirname(__DIR__));

// Register console commands
$app->registerCommands(
    new CommandNamespace(
        namespace: 'App\\Console\\Commands',
        path: dirname(__DIR__) . '/app/Console/Commands'
    )
);

// Configure database-related paths
$app->bind('app.migrations_path', dirname(__DIR__) . '/app/Database/Migrations');
$app->bind('app.models_path', dirname(__DIR__) . '/app/Database/Models');
$app->bind('app.factories_path', dirname(__DIR__) . '/app/Database/Factories');
$app->bind('app.seeders_path', dirname(__DIR__) . '/app/Database/Seeders');

$app->handle();
```
### Console Commands
Create custom console commands using attributes:

```php
#[AsCommand('hello_world')]
class HelloWorldCommand extends Command
{
    public function run(): int
    {
        $this->getOutput()->info('Hello World from console!');
        return 0;
    }
}
```

#### Built-in Commands

Database Commands:
```bash
# Run database seeder
database:seed --seeder=DatabaseSeeder

# Run migrations
migrate

# Generate a new migration
make:migration Name

# Create a new model
make:model Name --table=table_name

# Create a new factory
make:factory FactoryName

# Create a new seeder
make:seeder Name
```

Queue and Scheduling:
```bash
# Start the queue worker
queue:work

# Run the scheduler
schedule:work
```

Development Tools:
```bash
# Generate application security key
key:generate

# List all available commands
list

# Generate a new mailable class
make:mail Name

# Start WebSocket server
ws:start
```

Command Details:

- `database:seed` - Seeds the database using the specified seeder class
- `key:generate` - Generates a new application key for session and cookie encryption
- `list` - Displays a list of all available console commands
- `make:factory` - Creates a new model factory class
- `make:mail` - Generates a new mailable class for sending emails
- `make:migration` - Creates a new database migration file
- `make:model` - Generates a new model class with optional table name specification
- `make:seeder` - Creates a new database seeder class
- `migrate` - Runs pending database migrations
- `queue:work` - Starts processing jobs from the queue
- `schedule:work` - Starts the task scheduler
- `ws:start` - Launches the WebSocket server

### Interactive Debugging

The framework provides an interactive debugging terminal that automatically activates when an exception occurs in console commands. This powerful tool allows you to inspect the stack trace, examine variables, and analyze the application state at the time of the error.

#### Basic Usage

When an exception occurs, you'll be dropped into an interactive debug session:

```bash
Debug mode: (type 'help' for available commands) _
```

#### Available Commands

1. `trace` - Display the full stack trace:
```bash
Debug mode: (type 'help' for available commands) trace
#0 SomeClass::someFunction
-> /path/to/file.php:123
#1 AnotherClass::anotherFunction
-> /path/to/another_file.php:456
```

2. `frame [number]` - Examine a specific stack frame:
```bash
Debug mode: (type 'help' for available commands) frame 0
Frame #0 Details:
Location: /path/to/file.php:123
Call: SomeClass::someFunction(arg1, arg2)
Source:
> 123| problematic_function_call();
   124| next_line_of_code();
```

3. `help` - Display available commands
4. `exit` - Exit the debugging session

#### Key Features
- Interactive command prompt
- Stack trace inspection
- Source code preview around the error
- Function call details with arguments
- Frame-by-frame navigation

#### Tips
- Use `frame` command to move up and down the stack trace
- Examine the source code context around the error
- Check function arguments at each stack frame
- Use `trace` to get an overview of the call stack

### Routing & Controllers
Define routes using attributes with support for parameters and middleware:

```php
#[RouteGroup('views')]
class ControllerRenderingViews extends Controller
{
    #[Route('blade')]
    #[Middleware(beforeGlobal: [LogRequests::class])]
    public function index(): ResponseInterface
    {
        $this->withRenderer('blade');
        return $this->render('index.blade.php', ['user' => 'test']);
    }

    #[Route('twig/<category:\d+>')]
    public function category(Category $category): ResponseInterface
    {
        $this->withRenderer('twig');
        return $this->render('index.twig', ['user' => 'test']);
    }
}
```

### Database

#### Migrations
Create database structure using type-safe migration classes:

```php
return new class extends Migration
{
    public function up(): void
    {
        $this->schema->createTable('categories', [
            new IntColumn('id', nullable: false, autoIncrement: true),
            new VarcharColumn('name'),
            new TextColumn('description'),
            new DateTimeColumn('created_at', current: true),
            new DateTimeColumn('updated_at', currentOnUpdate: true),
            new PrimaryColumn('id'),
        ]);
    }
}
```

#### Models
Define models with property typing and automatic change tracking:

```php
class Category extends Model
{
    public string $table {
        get => 'categories';
    }

    #[FakeAs(FakerMethod::NAME)]
    public string $name {
        get => $this->name;
        set {
            $this->name = $value;
            $this->markPropertyAsChanged('name');
        }
    }
    
    protected array $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
```

#### Relationships
Define relationships between models using attributes:

```php
class Company extends Model
{
    public string $table {
        get => 'companies';
    }

    // One-to-Many relationship
    #[HasMany(Post::class, foreign_key: 'company_id', local_key: 'id')]
    public array $posts {
        get => $this->relations->getRelation('posts');
    }

    // Has-Many-Through relationship
    #[HasManyThrough(Comment::class, Post::class, 'company_id', 'post_id', 'id', 'id')]
    public array $comments {
        get => $this->relations->getRelation('comments');
    }
}
```

Available relationship attributes:
- `#[HasMany]` - One-to-Many relationship
- `#[HasManyThrough]` - Has-Many-Through relationship
- `#[BelongsTo]` - Belongs-To relationship
- `#[HasOne]` - One-to-One relationship


#### Seeders & Factories
Populate your database with test data:

```php
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        new CategoryFactory()->createMany(25);
    }
}
```

### Forms & Validation
Use attribute-based validation for form requests:

```php
class LoginFormDTO extends FormRequest
{
    #[Required]
    #[IsValidated]
    #[MaxLength(255)]
    public protected(set) string $login;

    #[Required]
    #[IsValidated]
    public protected(set) string $password;
}
```

### Events
Implement event-driven architecture:

```php
// Dispatch events
$this->events->dispatch(new UserRegisteredEvent($user));

// Create listeners
class EmailNotificationListener
{
    public function __invoke(UserRegisteredEvent $event): void
    {
        MailerFactory::createSmtpMailer(...Config::get('mail.default'))
            ->send(new WelcomeMailable($event->user));
    }
}
```

### WebSocket
Implement real-time features using WebSocket:

```php
#[AsCommand('ws:start')]
class WsStart extends Command
{
    public function run(): int
    {
        $loop = \React\EventLoop\Loop::get();
        $server = new WebSocketServer($loop, '0.0.0.0', 8080);
        $server->addListener('message', new MessageListener()->listen(...));
        $server->run();
        return 0;
    }
}
```

### Caching
Use the Cache facade for Redis or file-based caching:

```php
Cache::set('key', 'value', 3600); // Store for 1 hour
$value = Cache::get('key'); // Retrieve value
```

### Logging

The framework provides a PSR-compatible logging system with support for multiple channels and handlers. Logs can be stored in either database or files, with configurable formatters and retention periods.

#### Configuration (config/logging.php)

```php
return [
    'default' => 'database',

    'channels' => [
        'stack' => [
            'handler' => 'file',
            'path' => __DIR__.'/../storage/logs/app.log',
            'formatter' => 'text',
            'max_days' => 14
        ],

        'database' => [
            'handler' => 'database'
        ],

        'daily' => [
            'handler' => 'file',
            'path' =>  __DIR__.'/../storage/logs/daily.log',
            'formatter' => 'json',
            'max_days' => 7
        ]
    ]
];
```

#### Database Structure

If using database logging, the following migration creates the required table:

```php
$this->schema->createTable('database_logs', [
    new IntColumn('id', nullable: false, autoIncrement: true),
    new VarcharColumn('level'),
    new TextColumn('message'),
    new TextColumn('metadata'),
    new TextColumn('context'),
    new DateTimeColumn('created_at', current: true),
    new DateTimeColumn('updated_at', currentOnUpdate: true),
    new PrimaryColumn('id'),
]);
```


#### Usage

Log messages using the Log facade:

```php
use DJWeb\Framework\Log\Log;

// Basic logging
Log::info('User logged in');

// Logging with context
Log::info('Payment processed', [
    'user_id' => 123,
    'amount' => 99.99
]);
```

#### Available Log Levels
- `emergency`
- `alert`
- `critical`
- `error`
- `warning`
- `notice`
- `info`
- `debug`

#### Features
- PSR-3 compatible logging interface
- Multiple logging channels support
- File and database handlers
- JSON and text formatters
- Configurable log retention
- Context and metadata support
- Automatic timestamps

#### Channel Types
1. **File Handler**
    - Text or JSON formatting
    - Configurable file paths
    - Log rotation with max days setting

2. **Database Handler**
    - Structured log storage
    - Easy querying and analysis
    - Automatic timestamps
    - Metadata and context storage

3. **Stack Handler**
    - Combine multiple handlers
    - Flexible log routing


## Contributing
[Contributing guidelines to be added]

## License
[License information to be added]