
SUPERVISOR_IP=$(docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' supervisor)

export SUPERVISOR_IP

docker-compose up -d

