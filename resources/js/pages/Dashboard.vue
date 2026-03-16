<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { 
    CreditCard,
    CheckCircle2, 
    Clock, 
    XCircle,
    TrendingUp
} from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

defineProps<{
    stats: {
        total: number;
        paid: number;
        pending: number;
        expired: number;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-8 rounded-xl p-4 md:p-6 lg:max-w-7xl mx-auto w-full">
            
            <!-- Welcome Header -->
            <div class="flex flex-col gap-1">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                    Dashboard Overview
                </h2>
                <p class="text-gray-500 dark:text-gray-400">
                    Your payment transaction summary and performance at a glance.
                </p>
            </div>

            <!-- Stats Grid -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- Total Transactions -->
                <div class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-gray-200 dark:border-zinc-800 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2 bg-indigo-50 dark:bg-indigo-500/10 rounded-lg">
                            <CreditCard class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                        </div>
                        <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 px-2 py-1 rounded-full">
                            +12.5%
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Transactions</p>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.total.toLocaleString() }}</h3>
                    </div>
                </div>

                <!-- Paid -->
                <div class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-gray-200 dark:border-zinc-800 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2 bg-emerald-50 dark:bg-emerald-500/10 rounded-lg">
                            <CheckCircle2 class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <span class="text-xs font-medium text-gray-400">Paid Orders</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Paid Transactions</p>
                        <h3 class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ stats.paid.toLocaleString() }}</h3>
                    </div>
                </div>

                <!-- Pending -->
                <div class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-gray-200 dark:border-zinc-800 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2 bg-amber-50 dark:bg-amber-500/10 rounded-lg">
                            <Clock class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                        </div>
                        <span class="text-xs font-medium text-gray-400">Waiting</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pending Transactions</p>
                        <h3 class="text-3xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ stats.pending.toLocaleString() }}</h3>
                    </div>
                </div>

                <!-- Expired -->
                <div class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-gray-200 dark:border-zinc-800 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-2 bg-rose-50 dark:bg-rose-500/10 rounded-lg">
                            <XCircle class="w-6 h-6 text-rose-600 dark:text-rose-400" />
                        </div>
                        <span class="text-xs font-medium text-gray-400">No Action</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Expired Transactions</p>
                        <h3 class="text-3xl font-bold text-rose-600 dark:text-rose-400 mt-1">{{ stats.expired.toLocaleString() }}</h3>
                    </div>
                </div>
            </div>

            <!-- Placeholder for Chart or Recent Activity -->
            <div class="grid gap-6 md:grid-cols-2">
                 <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-2xl p-6 shadow-sm flex flex-col items-center justify-center min-h-[300px] text-center">
                    <div class="p-4 bg-gray-50 dark:bg-zinc-800 rounded-full mb-4">
                        <TrendingUp class="w-10 h-10 text-gray-400" />
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Transaction Performance</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 max-w-xs">Detailed analytics and growth charts will appear here as more data is collected.</p>
                 </div>
                 
                 <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-2xl p-6 shadow-sm flex flex-col items-center justify-center min-h-[300px] text-center">
                    <div class="p-4 bg-gray-50 dark:bg-zinc-800 rounded-full mb-4">
                         <CreditCard class="w-10 h-10 text-gray-400" />
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Payouts</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 max-w-xs">Your latest payout history and settled balances will be listed in this section.</p>
                 </div>
            </div>
            
        </div>
    </AppLayout>
</template>
