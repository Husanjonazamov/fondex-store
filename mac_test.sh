#!/bin/bash

set -e

echo "========== MAC PERFORMANCE TEST =========="
echo "Boshlangan vaqt: $(date)"
echo

echo "----- SYSTEM INFO -----"
echo "Hostname: $(hostname)"
echo "macOS: $(sw_vers -productName) $(sw_vers -productVersion)"
echo "Kernel: $(uname -r)"
echo "CPU: $(sysctl -n machdep.cpu.brand_string 2>/dev/null || echo 'Apple Silicon / Unknown')"
echo "Cores: $(sysctl -n hw.ncpu)"
echo "Memory: $(( $(sysctl -n hw.memsize) / 1024 / 1024 / 1024 )) GB"
echo

echo "----- CPU TEST (single process) -----"
START=$(date +%s)
python3 - <<'PY'
x = 0
for i in range(50_000_000):
    x += i % 7
print("CPU result:", x)
PY
END=$(date +%s)
echo "CPU test time: $((END - START)) sec"
echo

echo "----- CPU TEST (multi-core) -----"
START=$(date +%s)
python3 - <<'PY'
from multiprocessing import Pool, cpu_count

def work(n):
    s = 0
    for i in range(20_000_000):
        s += i % 5
    return s

if __name__ == "__main__":
    workers = cpu_count()
    with Pool(workers) as p:
        results = p.map(work, range(workers))
    print("Workers:", workers)
    print("Total:", sum(results))
PY
END=$(date +%s)
echo "Multi-core CPU test time: $((END - START)) sec"
echo

echo "----- DISK WRITE TEST -----"
rm -f testfile.bin
START=$(date +%s)
dd if=/dev/zero of=testfile.bin bs=64m count=16 2>&1
END=$(date +%s)
echo "Disk write time: $((END - START)) sec"
echo

echo "----- DISK READ TEST -----"
START=$(date +%s)
dd if=testfile.bin of=/dev/null bs=64m 2>&1
END=$(date +%s)
echo "Disk read time: $((END - START)) sec"
echo

echo "----- LOCAL HTTP REQUEST TEST -----"
python3 -m http.server 8000 >/tmp/mac_test_server.log 2>&1 &
SERVER_PID=$!
sleep 2

START=$(date +%s)
seq 1 200 | xargs -n1 -P20 curl -s http://127.0.0.1:8000 >/dev/null
END=$(date +%s)

kill $SERVER_PID 2>/dev/null || true
echo "200 local requests with concurrency 20: $((END - START)) sec"
echo

echo "----- MEMORY PRESSURE -----"
memory_pressure 2>/dev/null || echo "memory_pressure buyrug'i natija bermadi"
echo

rm -f testfile.bin

echo "========== TEST FINISHED =========="
echo "Tugagan vaqt: $(date)"
