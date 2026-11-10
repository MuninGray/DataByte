#!/usr/bin/env bash
set -Eeuo pipefail
HERE="$(cd "$(dirname "$0")" && pwd)"
source "$HERE/common.sh"
BAD=0
while IFS= read -r -d '' f; do
  if (cd "$(dirname "$f")" && sha256sum -c "$(basename "$f")") >/dev/null 2>&1; then echo "OK $f"; else echo "ERROR $f"; BAD=1; fi
done < <(find "$BACKUP_ROOT" -type f -name '*.sha256' -print0)
exit "$BAD"
