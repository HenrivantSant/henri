## 3. Configuration
The system uses [Yaml](https://yaml.org/) for configuration.
### Basic setup
The basic configuration setup is as below. Place this config.yaml file in the root of your project.
```yaml
database:
  driver: mysqli
  host: localhost
  username: foo
  password: bar
  database: foo_bar
  port: 3306
  prefix: fkhe32_

routing:
  baseurl: www.foo.bar

app:
  mode: develop
  debug: true
  timezone: Europe/Amsterdam  
```
#### Using app configuration
It is not unthinkable that you might want some configuration for you specific app. This is possible. First make sure to add a config.yaml file the app directory. This is the entry point for app configuration. Here you import your app config.
```yaml
imports:
  - { resource: app/Foo/config.yaml }
```
In the app/Foo directory also place a config.yaml file (as imported above)
```yaml
foo:
    bar: example
    lorem: ipsum
```

### Configuration scopes
The configuration is build in scopes. The root configuration has a scope as well, but can be ignored. The configuration in the file 'app/Foo/config.yaml' Will be in the scope 'app/Foo'. The idea behind is to isolate configuration in groups.

### Reading the configuration
To read the configuration you will have to inject the `Henri\Framework\Configuration\Configuration` class. Simply calling the 'get' method is enough. To get the value of bar from the example above would like like this `$this->configuration->get('foo.bar', 'app/Foo');`. The first argument is the name of the setting and the second one is the scope. Reading the root configuration uses the 'root' as scope. Checking whether the app in debug mode would work like this `$this->configuration->get('app.debug', 'root');` or getting the database username: `$this->configuration->get('database.username', 'root');`.

### Writing the configuration (in code)
Writing the configuration works in the exact same matter. Note that is not possible to write to non existing settings. Make sure the already exist before. Writing to the foo.bar setting as above would work as `$this->configuration->set('foo.bar', 'writing example', 'app/Foo');`. Note that this works exactly the same as getting a setting, except now the second parameter is the new value you wish to assign.