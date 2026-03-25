# fw-pegel

A lightweight PHP API server built with Slim Framework.

## What it does

- exposes endpoints for sensor readings
- controlls wwrite access using API key
- renders a minimal view - maybe later showing a chart with sensor data

## Structure

- `src/Controller` - route controllers
- `src/Middleware` - request middleware
- `src/Repository` - data access layer
- `src/Service` - business logic
- `src/view` - view templates 

## Run (devcontainer)

1. Open the folder in VS Code and launch the devcontainer.
2. Visit `http://localhost:8080`
