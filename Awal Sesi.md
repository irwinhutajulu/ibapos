Awal Sesi



run this before





cd "c:\\xampp\\htdocs\\Data IBA POS\\IBAPOS"

\# collect quick info

php -v

composer --version

node -v

git rev-parse --abbrev-ref HEAD

git status --porcelain



\# run diagnostics script (created in repo)

powershell -ExecutionPolicy Bypass -File .\\scripts\\collect-diagnostics.ps1



\# tail logs (run in separate terminal while reproducing)

Get-Content .\\storage\\logs\\laravel.log -Tail 200 -Wait

Get-Content 'C:\\xampp\\apache\\logs\\error.log' -Tail 200 -Wait





PROMPT





Kamu adalah GitHub Copilot, seorang master dalam coding. Baca seluruh file di folder Project Documentation, terutama CHANGELOG, PROGRESS, ACTION-PLAN, dan SPATIE-PERMISSION. Pahami arsitektur, status, dan progres terakhir fitur role \& permission, serta keputusan teknis selama sesi sebelumnya. Lanjutkan pengembangan sesuai dokumentasi terbaru.

