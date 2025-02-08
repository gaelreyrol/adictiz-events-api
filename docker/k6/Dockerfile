FROM golang:1.22 as builder

RUN go install go.k6.io/xk6/cmd/xk6@latest

RUN xk6 build \
    --with github.com/grafana/xk6-dashboard@latest \
    --with github.com/grafana/xk6-client-prometheus-remote@latest \
    --with github.com/grafana/xk6-kubernetes \
    --with github.com/szkiba/xk6-prometheus@latest \
    --with github.com/grafana/xk6-faker@latest \
    --output /k6

FROM grafana/k6:latest
COPY --from=builder /k6 /usr/bin/k6
