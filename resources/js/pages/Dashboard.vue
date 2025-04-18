<script setup lang="ts">
    import AppLayout from '@/layouts/AppLayout.vue';
    import { type BreadcrumbItem } from '@/types';
    import { Head } from '@inertiajs/vue3';
    import axios from 'axios';
    import { ref, onMounted, onUnmounted, computed } from 'vue';

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Dashboard',
            href: '/dashboard',
        },
    ];


    interface Job {
        id: number
        class: string
        method: string
        status: string
        attempts: number
        output: string | null
        execute: boolean
        created_at: string
    }

    const jobs = ref<Job[]>([]);

    const fetchJobs = async () => {
        try {
            const response = await axios.get<Job[]>('/jobs');
            jobs.value = response.data;
        } catch (error) {
            console.error('Failed to fetch jobs', error);
        }
    };


    const logs = ref<string>('Loading logs...')

    const fetchLogs = async () => {
        try {
            const res = await axios.get('/background_jobs_errors')
            logs.value = res.data.log
        } catch (err) {
            logs.value = 'Failed to load logs.'
            console.error(err)
        }
    }


    const job = ref<JobInput>({
        class: '',
        method: '',
        params: ''
    })

    const cancelJob = async (id: number) => {

        try {
            await axios.post(`/cancel/${id}`)
            console.log('Canceled job', id)
            fetchJobs()
        } catch (e) {
            console.error('Cancel failed', e)
        }
    }

    onMounted(() => {
        fetchLogs()
        fetchJobs()
        setInterval(fetchJobs, 3000) // Every x m seconds
        setInterval(fetchLogs, 3000) // Every x m seconds
    })


</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <div>
                    <h2 class="text-xl font-bold mb-4">Job Queue</h2>
                    <ul class="space-y-2">
                        <li v-for="job in jobs" :key="job.id" class="border p-4 rounded shadow">
                            <p><strong>{{ job.class }}::{{ job.method }}</strong></p>
                            <p>Status: <span :class="job.status">{{ job.status }}</span></p>
                            <p>Attempts: {{ job.attempts }}</p>
                            <p v-if="job.output">Output: {{ job.output }}</p>
                            <button @click="cancelJob(job.id)"
                                    :disabled="!job.execute"
                                    :class="['px-4', 'py-2', 'rounded', !job.execute ? 'bg-gray-400 text-gray-700' : 'bg-blue-600 text-white']">
                                Cancel
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <h2 class="text-xl font-bold mb-4">Error logs</h2>
                <div class="p-4 bg-black text-green-400 font-mono overflow-auto h-96">
                    <pre>{{ logs }}</pre>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
