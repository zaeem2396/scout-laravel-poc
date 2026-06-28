#!/usr/bin/env bash
set -euo pipefail

BASE_URL="${1:-http://localhost:8088}"
ROUNDS="${2:-30}"
SLEEP_SECONDS="${3:-2}"

DEMO_ROUTES=(
    "/demo/n-plus-one?limit=100"
    "/demo/n-plus-one?limit=150"
    "/demo/slow-query"
    "/demo/slow-method"
    "/demo/cache?reset=1"
    "/demo/cache"
    "/demo/events"
    "/demo/jobs"
    "/demo/request"
    "/demo/dashboard"
    "/demo/memory"
    "/products"
)

echo "Generating Scout APM traffic against ${BASE_URL}"
echo "Rounds: ${ROUNDS}, pause: ${SLEEP_SECONDS}s between requests"
echo ""

for ((round = 1; round <= ROUNDS; round++)); do
    route="${DEMO_ROUTES[$((RANDOM % ${#DEMO_ROUTES[@]}))]}"
    url="${BASE_URL}${route}"
    status="$(curl -s -o /dev/null -w "%{http_code}" "${url}" || echo "000")"
    echo "[${round}/${ROUNDS}] ${status} ${route}"

  sleep "${SLEEP_SECONDS}"
done

echo ""
echo "Done. Check Scout APM dashboard (may take 1-3 minutes to populate)."
