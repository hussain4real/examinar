<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Dashboard Overview --}}
        <x-filament::section icon="heroicon-o-chart-bar" heading="Dashboard Overview" description="Understanding your admin dashboard">
            <div class="prose dark:prose-invert max-w-none text-sm">
                <p>The admin dashboard gives you a quick snapshot of your platform:</p>
                <ul>
                    <li><strong>Published Exams</strong> — Total number of exams available for sessions.</li>
                    <li><strong>Active Sessions</strong> — Currently running exam sessions students can join.</li>
                    <li><strong>Registered Students</strong> — Total student accounts on the platform.</li>
                    <li><strong>Server Info</strong> — Shows your LAN IP address and server URL so students can connect.</li>
                </ul>
            </div>
        </x-filament::section>

        {{-- Managing Exams --}}
        <x-filament::section icon="heroicon-o-clipboard-document-list" heading="Managing Exams" description="Creating and organizing your exam content">
            <div class="prose dark:prose-invert max-w-none text-sm">
                <p>Exams are the foundation of your assessments. Here's the typical workflow:</p>
                <ol>
                    <li><strong>Create an Exam</strong> — Go to <em>Exam Management → Exams → New Exam</em>. Fill in the title, description, duration (minutes), and pass score (percentage).</li>
                    <li><strong>Add Questions</strong> — On the exam view page, use the Questions section to add questions. Each question has a type (MCQ or True/False), a body, points, and the correct answer.</li>
                    <li><strong>Set Options</strong> — For MCQ questions, add answer options. One must match the correct answer exactly.</li>
                    <li><strong>Publish</strong> — Change the exam status from <em>Draft</em> to <em>Published</em> when ready. Only published exams can be used in sessions.</li>
                </ol>
                <p><strong>Tip:</strong> Use the <em>Shuffle Questions</em> toggle to randomize question order per student.</p>
            </div>
        </x-filament::section>

        {{-- Exam Sessions --}}
        <x-filament::section icon="heroicon-o-play-circle" heading="Exam Sessions" description="Running and monitoring live exams">
            <div class="prose dark:prose-invert max-w-none text-sm">
                <p>Sessions control when students can take an exam:</p>
                <ol>
                    <li><strong>Create a Session</strong> — Go to <em>Exam Management → Exam Sessions → New Session</em>. Select a published exam.</li>
                    <li><strong>Start the Session</strong> — Set the status to <em>Active</em>. Students in the Lobby will see it appear in real time.</li>
                    <li><strong>Monitor Progress</strong> — On the session view page, the Attempts tab shows each student's status, score, and flag count.</li>
                    <li><strong>End the Session</strong> — Set status to <em>Completed</em>. Any students still taking the exam will be force-submitted automatically.</li>
                </ol>
                <div class="rounded-lg border border-amber-300 bg-amber-50 p-3 text-amber-800 not-prose text-sm dark:border-amber-800 dark:bg-amber-950/50 dark:text-amber-200">
                    <strong>Important:</strong> Once a session is completed, students cannot rejoin. Make sure all students have finished before ending.
                </div>
            </div>
        </x-filament::section>

        {{-- Student Management --}}
        <x-filament::section icon="heroicon-o-users" heading="Student Management" description="Managing student accounts">
            <div class="prose dark:prose-invert max-w-none text-sm">
                <p>Students are users who take exams. You can:</p>
                <ul>
                    <li><strong>Create Students</strong> — Add them manually with name, email, and password via <em>User Management → Students</em>.</li>
                    <li><strong>View Details</strong> — Click a student to see their profile and exam attempt history.</li>
                    <li><strong>Track Activity</strong> — The student list shows the number of exam attempts per student.</li>
                </ul>
                <p>Students log in with their email and password and are automatically directed to the Student Dashboard.</p>
            </div>
        </x-filament::section>

        {{-- Anti-Cheat Monitoring --}}
        <x-filament::section icon="heroicon-o-shield-exclamation" heading="Anti-Cheat Monitoring" description="Understanding the integrity system">
            <div class="prose dark:prose-invert max-w-none text-sm">
                <p>Examinar automatically monitors student behavior during exams. The following events are tracked:</p>
                <div class="not-prose grid gap-2 sm:grid-cols-2 my-3">
                    <div class="rounded-lg border p-3">
                        <p class="font-medium">Tab Switches</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Student switches to another browser tab or window.</p>
                    </div>
                    <div class="rounded-lg border p-3">
                        <p class="font-medium">Fullscreen Exits</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Student leaves fullscreen mode during the exam.</p>
                    </div>
                    <div class="rounded-lg border p-3">
                        <p class="font-medium">Copy Attempts</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Student tries to copy exam content.</p>
                    </div>
                    <div class="rounded-lg border p-3">
                        <p class="font-medium">Right Clicks</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Student opens the context menu.</p>
                    </div>
                </div>
                <p>To review a student's activity:</p>
                <ol>
                    <li>Go to the <strong>Exam Session → View</strong> page.</li>
                    <li>In the <strong>Attempts</strong> tab, click the <strong>eye icon</strong> on any attempt.</li>
                    <li>The modal shows attempt details and a chronological <strong>Anti-Cheat Logs</strong> section with event types, context (question number, time remaining), and timestamps.</li>
                </ol>
                <p>The <strong>Flagged Students</strong> count on the session view page shows how many students have at least one flag.</p>
            </div>
        </x-filament::section>

    </div>
</x-filament-panels::page>
