TAG?=16-alpine
CONTAINER?=$(shell basename $(dir $(CURDIR)))-buildchain
DOCKERRUN=docker container run \
	--name ${CONTAINER} \
	--rm \
	-t \
	-e CPPFLAGS="-DPNG_ARM_NEON_OPT=0" \
	-v "${CURDIR}"/../:/app \
	${CONTAINER}:${TAG}

.PHONY: build docker install clean npm

# Build the Twigfield asset bundle resources into web/assets/dist
build: docker install
	${DOCKERRUN} \
		run build
# Remove node_modules/ and package-lock.json to start clean
clean:
	rm -rf node_modules/
	rm -f package-lock.json
# Build the Docker image from the Dockerfile
docker:
	docker build \
		. \
		-t ${CONTAINER}:${TAG} \
		--build-arg TAG=${TAG} \
		--no-cache
# Run npm install inside of the Docker container
install: docker
	${DOCKERRUN} \
		install
# Run the passed in npm command inside of the Docker container
npm: docker
	${DOCKERRUN} \
		$(filter-out $@,$(MAKECMDGOALS))
%:
	@:
# ref: https://stackoverflow.com/questions/6273608/how-to-pass-argument-to-makefile-from-command-line
