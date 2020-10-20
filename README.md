# henri

## Looking for contributors!

## Intro
This is a simple, fast and basic PHP framework meant to write API's or simple programs in a fast and easy way. This is a not meant as a replacement for other frameworks,
under the hood it uses a lot of the magic from [Symfony](https://symfony.com/), [Dibi](https://github.com/dg/dibi), [Unirest](https://github.com/Kong/unirest-php), [Monolog](https://github.com/Seldaek/monolog) and [Firebase](https://github.com/firebase/php-jwt).

### Purpose  
This framework is not intended for building websites or big applications. The purpose of this framework is to provide a simple set of basic tools to build simple services, like: 
- Simple webservice
- API proxy to bundle several APIs endpoints into one
- Data caching layer
- API endpoint for React/Vue front-ends
- Logging service
- CDN

## Table of content
1. Routing
	1. Controllers
	1. Route annotation
	1. Responses
	1. Hooking in to the router (route events)
1. Dependency Injection
1. Configuration
	1. Basic setup
	1. Configuration scopes
	1. Reading the configuration
	1. Writing the configuration
1. Database handling
	1. Database layer
	1. Entities
	1. Entity Manager
	1. Entity Manager List
	1. Command line interface
1. Making (curl) requests
	1. Request service
1. Command Line
	1. Intro
	1. Default commands
	1. Make your own commands
1. Annotations
	1. What & why annotations
	1. How use your own annotations
1. Events & subscribers
	1. Default system events
	1. How to subscribe to events
	1. How to create your events
1. Logging (Monolog)
	1. Native logging
	1. Configuration
	1. Ways of logging
	1. Use your logger
1. Authentication
	1. Authentication levels
	1. API Key
	1. JWT
	1. User logins
	1. Add your level and/or authentication
1. Users
	1. User management
	1. Create a user
	1. Update user
	1. User authentication
1. What's next!
	1. Native logging interface (status: in development)
	Logging implementation using [Monolog](https://github.com/Seldaek/monolog)
	1. Out of the box GraphQL support (status: expected early 2021)
	1. PHP8 Compatibility (status: expected early 2021)
	1. Overriding framework classes by setting preferences to the container (status: no expection yet)
	1. Influence DI behaviour using Annotations (status: no expectation yet)
	1. Default annotation reading service with PHP8 Annotations support (status: no expectation yet)
