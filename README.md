# NirmanNeeds - A Dockerized Wordpress E-Commerce website

As part of a personal project, this is a Wordpress e-commerce website with containerised deployment through Docker.
It is meant for experimentation and to be used by a local business, intending to digitalise their sales workflows.

## Technical Requirements

- Unix-based OS (Linux or MacOS recommended)
- Support for Docker (Tested with 28.3.3)
- Support for Docker Compose (Tested with 2.34.0)

## Setup

1. Clone the repository:

```bash
git clone git@github.com:harshlunia71/NirmanNeeds.git
cd NirmanNeeds
```

2. Install dependencies

- Linux

```bash
sudo apt update -y
sudo apt install docker docker-compose just
```

- MacOS (HomeBrew)

```bash
brew install docker docker-compose just
```

3. Build and start containers

```bash
just build
```
