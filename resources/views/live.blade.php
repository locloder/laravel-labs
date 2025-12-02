<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskFlow+ Live</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="p-10 bg-gray-100">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-4">Події у реальному часі (Проєкт #1)</h2>

        <div class="mb-4 p-4 bg-blue-50 text-blue-700 rounded">
            <p>Відкрийте цю сторінку. В іншій вкладці або через Postman змініть статус задачі або додайте коментар до проєкту ID 1.</p>
        </div>

        <div id="log" class="space-y-2">
            </div>
    </div>

    <script type="module">
        setTimeout(() => {
            const projectId = 1; 
            const logElement = document.getElementById('log');

            const log = (msg, type = 'info') => {
                const colors = {
                    info: 'bg-gray-100',
                    update: 'bg-yellow-100 border-l-4 border-yellow-500',
                    comment: 'bg-green-100 border-l-4 border-green-500'
                };

                const date = new Date().toLocaleTimeString();

                logElement.innerHTML = `
                    <div class="p-3 rounded ${colors[type]} shadow-sm transition-all duration-500">
                        <span class="text-xs text-gray-500 font-mono mr-2">[${date}]</span>
                        ${msg}
                    </div>
                ` + logElement.innerHTML;
            };

            console.log('Connecting to channel...');

            window.Echo.private(`project.${projectId}`)
                .listen('.task.updated', (e) => {
                    console.log('Task Updated:', e);
                    log(`🟡 Задача <b>"${e.title}"</b> змінена. Новий статус: <b>${e.status}</b>`, 'update');
                })
                .listen('.comment.created', (e) => {
                    console.log('Comment Created:', e);
                    log(`💬 Новий коментар до задачі #${e.task_id}: "${e.body}" (автор: ${e.author})`, 'comment');
                })
                .error((error) => {
                    console.error('Echo Error:', error);
                    log('❌ Помилка підключення (перевірте консоль/авторизацію).', 'info');
                });
        }, 1000);
    </script>
</body>
</html>
