<template>
    <div>
        <form class="w-full">
            <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        Label
                    </label>
                    <input
                        class="w-full py-3 px-4 mb-3 text-sm appearance-none block text-gray-700 border border-gray-300 rounded leading-tight focus:outline-none"
                        type="text"
                        placeholder="Car Insurance"
                        v-model="transactionCopy.label"
                    >
                </div>

                <div class="w-full md:w-1/3 px-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        Date
                    </label>

                    <!-- there is a padding issue with this datetime-local, hard-coding via style to get it well aligned -->
                    <input
                        style="padding-top:10px; padding-bottom: 10px;"
                        class="py-3 px-4 text-sm appearance-none block w-full text-gray-700 border border-gray-300 rounded focus:outline-none focus:bg-white focus:border-gray-500"
                        type="datetime-local"
                        v-model="transactionCopy.transaction_at"
                    >
                </div>

                <div class="w-full md:w-1/3 px-3">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        Amount
                    </label>
                    <input
                        class="py-3 px-4 text-sm appearance-none block w-full text-gray-700 border border-gray-300 rounded focus:outline-none focus:bg-white focus:border-gray-500"
                        type="number"
                        placeholder="$ - 500.00"
                        v-model="transactionCopy.amount"
                    >
                </div>
            </div>

            <div class="pb-2 pr-1 flex justify-end items-center">
                <button
                    class="py-4 px-5 rounded items-center text-xs font-bold tracking-wider bg-gray-300 hover:bg-gray-400 text-gray-600"
                    @click.prevent="$emit('cancel');"
                >CANCEL
                </button>

                <button
                    class="py-4 px-5 ml-3 rounded items-center text-xs font-bold tracking-wider bg-primary hover:bg-blue-900 text-white"
                    @click.prevent="updateTransaction"
                >UPDATE ENTRY
                </button>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                transactionCopy: null
            }
        },

        props: [
            'transaction',
        ],

        beforeMount() {
            this.transactionCopy = _.cloneDeep(this.transaction);
        },

        methods: {
            updateTransaction() {
                axios.put(`/transactions/${this.transactionCopy.id}`, this.transactionCopy).then(res => {
                    this.$emit('success', this.transactionCopy, this.transaction);
                }).catch(err => {
                    // TODO
                    alert('Something went wrong');
                })
            }
        }
    }
</script>
