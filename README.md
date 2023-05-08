<h1>SULU init</h1>

Preconfigured [Sulu](https://sulu.io) installation for a quicker start of new project  
`symfony 6.2`, `sulu 2.5`

## Fist steps:
- rename default webspace
- `docker-compose build` && `docker-compose up`
- `docker exec -it app sh` && `bin/console sulu:build dev`

## Features
- preconfigured docker dev enviroment
- basic website structure (css resets, layouts, components)
- lots of preconfigured and prestyled sulu blocks 
- simple page-list service that can be used for limited blog/article functionality

### Disclaimers
page-list service is meant to be used for page lists or page-based blogs/news. This setup can run on shared webhosting. If you need full-featured article service, please use [sulu/SuluArticleBundle](https://github.com/sulu/SuluArticleBundle)
