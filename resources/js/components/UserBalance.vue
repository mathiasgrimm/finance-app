<template>
    <div class="">
        <div class="bg-teal-100 h-32 bg-secondary">

            <!-- Page Banner -->
            <div class="h-full container mx-auto max-w-3xl">
                <div class="h-full flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="text-xl text-white font-semibold">Your Balance</div>

                        <button
                            class="h-8 ml-4 inline-flex text-xs bg-primary hover:bg-blue-800 text-white font-bold py-2 px-3 rounded items-center"
                            @click="showingAddTransactionModal = !showingAddTransactionModal"
                        >
                            <img class="object-contain w-3" src="/img/add.svg"><span class="ml-1">ADD ENTRY</span>
                        </button>

                        <button class="h-8 ml-4 overflow-hidden inline-flex text-xs bg-primary hover:bg-blue-800 text-white font-bold py-3 px-3 rounded items-center">
                            <img class="object-contain w-6 -ml-2" src="/img/import.svg"><span class="">IMPORT CSV</span>
                        </button>
                    </div>

                    <div class="text-right mr-4">
                        <span class="text-sm font-bold text-gray-500">TOTAL BALANCE</span>
                        <br>
                        <div :class="totalBalance >= 0 ? 'text-green-balance' : 'text-red-500'" v-if="!isLoading">
                            <span class="text-4xl">{{ totalBalance < 0 ? '-' : ''}} {{ formatAmount(totalBalance, 'first') }}</span><span class="text-xl">{{ formatAmount(totalBalance, 'second') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="container max-w-3xl mx-auto mt-10 mb-16">
                <div v-if="isLoading">
                    loading...
                </div>

                <div v-if="groupedTransactions && Object.keys(groupedTransactions).length < 1">
                    no transactions found
                </div>

                <div v-if="groupedTransactions" v-for="(transactions, date) in groupedTransactions" :key="date">
                    <div class="flex justify-between items-center mb-5 mt-10 text-sm">
                        <h2 class="uppercase text-sm text-gray-600 font-bold">
                            {{ formatDateHeader(date) }}
                        </h2>
                        <span class="mr-4 font-bold text-base" :class="amountColorClass(totalPerDate[date], true)">
                            <span>{{ formatAmount(totalPerDate[date], 'first', true) }}</span><span class="text-xs">{{ formatAmount(totalPerDate[date], 'second', false) }}</span>
                        </span>
                    </div>

                    <div v-for="transaction in transactions" class="py-3 mb-2 bg-white shadow rounded mt-4" :key="transaction.id">
                        <div class="flex flex-col">
                            <div
                                class="px-4 flex justify-between items-center mb-2"
                                @mouseenter="showActions(transaction)"
                                @mouseleave="hideActions(transaction)"
                            >
                                <div class="flex flex-col">
                                    <div class="font-semibold capitalize">{{ transaction.label }}</div>
                                    <div class="mt-2 text-xs font-medium text-gray-600">{{ formatTransactionDate(transaction.transaction_at) }}</div>
                                </div>

                                <div v-if="showingActionsForItemId === transaction.id" class="text-right w-64">
                                    <button
                                        class="text-primary font-semibold text-sm underline"
                                        @click.prevent="editTransaction(transaction)"
                                    >
                                        EDIT
                                    </button>

                                    <button
                                        class="ml-4 text-primary font-semibold text-sm underline"
                                        @click.prevent="deleteTransaction(transaction)"
                                    >
                                        DELETE
                                    </button>
                                </div>

                                <div class="font-semibold" :class="amountColorClass(transaction.amount, false)">
                                    {{ formatAmount(transaction.amount, 'first', true) }}<span class="text-xs">{{ formatAmount(transaction.amount, 'second', false) }}</span>
                                </div>
                            </div>

                            <div
                                v-if="showingEditTransactionForId === transaction.id"
                                class="px-4 pt-10 border-t border-1"
                            >
                                <edit-transaction
                                    :transaction="transaction"
                                    @success="transactionUpdated"
                                    @cancel="cancelEditingTransaction(transaction)"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- pagination -->
                <div v-if="meta && Object.keys(groupedTransactions).length > 0" class="flex items-center mt-4 justify-center">
                    <button
                        class="h-8 ml-4 inline-flex text-xs bg-primary hover:bg-blue-800 text-white font-bold py-2 px-4 rounded items-center"
                        :disabled="meta.current_page === 1"
                        :class="meta.current_page === 1 ? 'cursor-not-allowed' : ''"
                        @click.prevent="getTransactions(meta.current_page - 1)"
                    >
                        &laquo; PREV
                    </button>

                    <button
                        class="h-8 ml-4 inline-flex text-xs bg-primary hover:bg-blue-800 text-white font-bold py-2 px-4 rounded items-center"
                        :disabled="meta.current_page === meta.last_page"
                        :class="meta.current_page === meta.last_page ? 'cursor-not-allowed': ''"
                        @click.prevent="getTransactions(meta.current_page + 1)"
                    >
                        NEXT &raquo;
                    </button>
                </div>
            </div>
        </div>

        <add-transaction-modal
            v-model="showingAddTransactionModal"
            :user-id="userId"
            @cancel="showingAddTransactionModal = false"
            @success="transactionAdded"
        />
    </div>

</template>

<script>
    import AddTransactionModal from "./AddTransactionModal";
    import EditTransaction from "./EditTransaction";

    export default {
        components: {
            AddTransactionModal,
            EditTransaction,
        },

        props: {
            'userId': {
                required: true,
            }
        },

        data() {
            return {
                showingAddTransactionModal: false,
                groupedTransactions: null,
                totalBalance: 0.00,
                totalPerDate: null,
                meta: null,
                isLoading: false,
                showingActionsForItemId: null,
                showingEditTransactionForId: null,
            }
        },

        mounted() {
            this.getTransactions();
        },

        methods: {
            getTransactions(page) {
                if (!page) {
                    page = 1;
                }

                this.isLoading = true;
                this.groupedTransactions = null;
                this.meta = null;
                this.totalBalance = 0.00;

                axios.get(`/users/${this.userId}/transactions?page=${page}&include_total_per_date=1&include_total_balance=1`).then(res => {
                    this.meta = res.data.meta;

                    let groupedTransactions = {};
                    res.data.data.map(transaction => {
                        let date = moment.utc(transaction.transaction_at).format('YYYY-MM-DD');

                        if (!groupedTransactions[date]) {
                            groupedTransactions[date] = [];
                        }
                        groupedTransactions[date].push(transaction);
                    });

                    this.groupedTransactions = groupedTransactions;
                    this.totalBalance = res.data.total_balance;
                    this.totalPerDate = res.data.total_per_date;
                }).catch(err => {
                    // TODO
                    alert('something went wrong');
                }).then(() => {
                    this.isLoading = false;
                })
            },

            // TODO this whole method should be refactored
            formatAmount(totalBalance, part, includeSign) {
                let total = Math.abs(totalBalance.toFixed(2));

                // TODO this is a good candidate for rafactor - this should be extracted from this component
                // add commas to the amount
                total = total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

                let parts = total.split('.');
                let formatted = '';

                if (part === 'first') {
                    if (includeSign) {
                        if (totalBalance >= 0) {
                            formatted = '+ $';
                        } else {
                            formatted = '- $';
                        }
                    } else {
                        formatted = '$';
                    }

                    return formatted + parts[0];
                } else {
                    return '.' + (parts[1] ? parts[1].padEnd(2, '0') : '00');
                }
            },

            formatDateHeader(date) {
                let stDate = moment.utc(date).format('YYYY-MM-DD');
                let stToday = moment.utc().format('YYYY-MM-DD');
                let stYesterday = moment.utc().subtract(1, 'days').format('YYYY-MM-DD');

                if (stDate === stToday) {
                    return 'TODAY';
                } else if (stDate === stYesterday) {
                    return 'YESTERDAY';
                } else {
                    // mockup does not have the year but im including because it might be relevant
                    return moment.utc(date).format('DD MMM, YYYY');
                }
            },

            formatTransactionDate(date) {
                return moment.utc(date).format('DD MMM, YYYY [at] LT');
            },

            showActions(transaction) {
                this.showingActionsForItemId = transaction.id;
            },

            hideActions(transaction) {
                this.showingActionsForItemId = null;
            },

            deleteTransaction(transaction) {
                axios.delete(`/transactions/${transaction.id}`).then(res => {
                    this.getTransactions();
                }).catch(err => {
                    // TODO
                    alert('something went wrong')
                });
            },

            transactionAdded() {
                this.showingAddTransactionModal = false;
                this.getTransactions();
            },

            editTransaction(transaction) {
                this.showingEditTransactionForId = transaction.id;
            },

            transactionUpdated(updatedTransaction, oldTransaction) {
                this.showingEditTransactionForId = null;
                this.getTransactions();
            },

            cancelEditingTransaction(transaction) {
                this.showingEditTransactionForId = null;
            },

            amountColorClass(amount, isHeader) {
                if (amount >= 0) {
                    return 'text-green-balance';
                } else {
                    return isHeader ? 'text-gray-600' : 'text-gray-900';
                }
            }
        }
    }
</script>
