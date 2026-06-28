#!/usr/bin/env bash
set -euo pipefail

BASE_URL="${1:-http://localhost:8088}"
ROUNDS="${2:-30}"
LIMIT="${3:-100}"
SLEEP_SECONDS="${4:-1}"

URL="${BASE_URL}/demo/n-plus-one?limit=${LIMIT}"

echo "N+1 traffic -> ${URL}"
echo "Rounds: ${ROUNDS}, pause: ${SLEEP_SECONDS}s"
echo ""

for ((round = 1; round <= ROUNDS; round++)); do
    status="$(curl -s -o /dev/null -w "%{http_code} %{time_total}" "${URL}" || echo "000 0")"
    echo "[${round}/${ROUNDS}] ${status}"
    sleep "${SLEEP_SECONDS}"
done

echo ""
echo "Done. In Scout: Web Endpoints -> DemoController@nPlusOne, then N+1 Insights (may take 2-5 min)."
