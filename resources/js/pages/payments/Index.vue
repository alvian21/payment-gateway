<script setup lang="ts">
import { Head, router, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';
import { 
    Search, 
    Filter, 
    Flag, 
    CheckCircle2, 
    Clock, 
    XCircle, 
    CreditCard,
    AlertCircle
} from 'lucide-vue-next';

const props = defineProps<{
    payments: any;
    filters: any;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Payments',
        href: '/dashboard/payments',
    },
];

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || 'all');

watch(
    [search, status],
    debounce(() => {
        router.get(
            '/dashboard/payments',
            { search: search.value, status: status.value },
            { preserveState: true, replace: true, preserveScroll: true }
        );
    }, 300)
);

const toggleFlag = (id: number) => {
    router.post(`/dashboard/payments/${id}/flag`, {}, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Payments Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4 md:p-6 lg:max-w-7xl mx-auto w-full">
            
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white flex items-center gap-2">
                        <CreditCard class="w-6 h-6 text-indigo-500" />
                        Payments Management
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Manage and monitor all your payment orders and their current status.
                    </p>
                </div>
            </div>

            <!-- Filters Card -->
            <div class="bg-white dark:bg-zinc-900 shadow-sm border border-gray-200 dark:border-zinc-800 rounded-xl p-4">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="relative w-full sm:max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <Search class="h-4 w-4 text-gray-400" />
                        </div>
                        <input 
                            v-model="search"
                            type="text" 
                            placeholder="Search Reff or Customer Name..." 
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-zinc-700 rounded-lg leading-5 bg-gray-50 dark:bg-zinc-800 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors dark:text-white"
                        />
                    </div>
                    
                    <div class="relative w-full sm:w-auto flex items-center min-w-[200px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <Filter class="h-4 w-4 text-gray-400" />
                        </div>
                        <select 
                            v-model="status"
                            class="block w-full pl-10 pr-10 py-2 text-base border-gray-300 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg transition-colors dark:text-white appearance-none"
                        >
                            <option value="all">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="expired">Expired</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white dark:bg-zinc-900 shadow-sm border border-gray-200 dark:border-zinc-800 rounded-xl overflow-hidden flex-1">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-800">
                        <thead class="bg-gray-50/50 dark:bg-zinc-800/50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reference Info</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer Details</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-zinc-800">
                            <tr v-for="payment in payments.data" :key="payment.id" class="hover:bg-gray-50/50 dark:hover:bg-zinc-800/50 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white font-mono">{{ payment.reff }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Created: {{ new Date(payment.created_at).toLocaleDateString() }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ payment.customer_name }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ payment.hp }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        Rp {{ payment.amount.toLocaleString('id-ID') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="payment.status === 'paid'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                        <CheckCircle2 class="w-3.5 h-3.5" />
                                        Paid
                                    </span>
                                    <span v-else-if="payment.status === 'pending'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-500/10 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20">
                                        <Clock class="w-3.5 h-3.5" />
                                        Pending
                                    </span>
                                    <span v-else-if="payment.status === 'expired'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-rose-100 text-rose-800 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20">
                                        <XCircle class="w-3.5 h-3.5" />
                                        Expired
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <button 
                                        @click="toggleFlag(payment.id)"
                                        :class="[
                                            'inline-flex items-center justify-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-zinc-900',
                                            payment.flagged_at 
                                                ? 'bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:hover:bg-rose-500/20 focus:ring-rose-500' 
                                                : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-zinc-800 dark:text-gray-300 dark:hover:bg-zinc-700/80 focus:ring-gray-500'
                                        ]"
                                    >
                                        <Flag :class="['w-4 h-4', payment.flagged_at ? 'fill-current' : '']" />
                                        {{ payment.flagged_at ? 'Flagged' : 'Flag' }}
                                    </button>
                                    <div v-if="payment.flagged_at" class="text-[10px] text-rose-500 dark:text-rose-400 mt-1.5 font-medium">
                                        {{ new Date(payment.flagged_at).toLocaleDateString() }}
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Empty State -->
                            <tr v-if="payments.data.length === 0">
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="h-16 w-16 bg-gray-50 dark:bg-zinc-800 rounded-full flex items-center justify-center mb-4">
                                            <AlertCircle class="h-8 w-8 text-gray-400 dark:text-gray-500" />
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">No payments found</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                                            We couldn't find any transactions matching your current filters. Try relaxing your search criteria.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Pagination -->
                <div v-if="payments.links && payments.links.length > 3" class="px-6 py-4 flex items-center justify-between border-t border-gray-200 dark:border-zinc-800 bg-gray-50/30 dark:bg-zinc-800/30">
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-400">
                                Showing
                                <span class="font-medium text-gray-900 dark:text-white">{{ payments.from || 0 }}</span>
                                to
                                <span class="font-medium text-gray-900 dark:text-white">{{ payments.to || 0 }}</span>
                                of
                                <span class="font-medium text-gray-900 dark:text-white">{{ payments.total }}</span>
                                results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <template v-for="(link, key) in payments.links" :key="key">
                                    <component 
                                        :is="link.url ? Link : 'span'"
                                        :href="link.url"
                                        preserve-scroll
                                        v-html="link.label"
                                        :class="[
                                            'relative inline-flex items-center px-4 py-2 text-sm font-medium border first:rounded-l-md last:rounded-r-md transition-colors',
                                            link.active 
                                                ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600 dark:bg-indigo-500/10 dark:border-indigo-500/50 dark:text-indigo-400' 
                                                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-zinc-900 dark:border-zinc-700 dark:text-gray-400 dark:hover:bg-zinc-800',
                                            !link.url && 'opacity-50 cursor-not-allowed bg-gray-50 dark:bg-zinc-800/50'
                                        ]"
                                    />
                                </template>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </AppLayout>
</template>
