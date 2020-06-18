<template>
    <div
        class="h-screen fixed inset-0 w-full flex items-center justify-center bg-smoke shadow"
        v-if="value"
    >
        <div
            class="z-50 flex items-center justify-center"
        >
            <div class="bg-white rounded shadow max-h-screen text-center">
                <div class="mb-4 pl-10 py-8 border-b text-left">
                    <h1 class="text-2xl font-semibold">Add Balance Entry</h1>
                </div>
                <div class="mb-8">
                    <form class="w-full">
                        <div class="flex mb-6 border-b pb-4 pt-8">
                            <div class="w-full md:w-1/3 pl-8 mb-6 text-left">
                                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                                    Label
                                </label>
                                <input
                                    class="w-full py-3 px-4 mb-3 text-sm appearance-none block text-gray-700 border border-gray-300 rounded leading-tight focus:outline-none"
                                    type="text"
                                    placeholder="Car Insurance"
                                    v-model="form.label"
                                >
                            </div>

                            <div class="w-full md:w-1/3 ml-3 text-left">
                                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                                    Date
                                </label>

                                <!-- there is a padding issue with this datetime-local, hard-coding via style to get it well aligned -->
                                <input
                                    style="padding-top:10px; padding-bottom: 10px;"
                                    class="py-3 px-4 text-sm appearance-none block w-full text-gray-700 border border-gray-300 rounded focus:outline-none focus:bg-white focus:border-gray-500"
                                    type="datetime-local"
                                    v-model="form.transaction_at"
                                >
                            </div>

                            <div class="w-full md:w-1/3 ml-3 mr-8 text-left">
                                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                                    Amount
                                </label>
                                <input
                                    class="py-3 px-4 text-sm appearance-none block w-full text-gray-700 border border-gray-300 rounded focus:outline-none focus:bg-white focus:border-gray-500"
                                    type="number"
                                    placeholder="$ - 500.00"
                                    v-model="form.amount"
                                >
                            </div>
                        </div>

                        <div class=" pr-6 flex justify-end items-center">
                            <button
                                class="py-5 px-6 rounded items-center text-xs font-bold tracking-wider bg-gray-300 hover:bg-gray-400 text-gray-600"
                                @click.prevent="$emit('cancel');"
                            >CANCEL
                            </button>

                            <button
                                class="py-5 px-6 ml-4 rounded items-center text-xs font-bold tracking-wider bg-primary hover:bg-blue-900 text-white"
                                @click.prevent="addTransaction"
                            >SAVE ENTRY
                            </button>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                form: {
                    label: null,
                    transaction_at: null,
                    amount: null,
                }
            }
        },

        props: [
            'value',
            'userId'
        ],

        methods: {
            reset() {
                this.form = {
                    label: null,
                    date: null,
                    amount: null,
                }
            },

            toggleDisplay() {
                this.reset();
                this.$emit('input', !this.value);
            },

            addTransaction() {
                axios.post(`/users/${this.userId}/transactions`, this.form).then(res => {
                    this.$emit('success');
                }).catch(err => {
                    // TODO
                    alert('Something went wrong');
                })
            }
        }
    }
</script>

<style scoped>
    .bg-smoke {
        background-color: rgba(0, 0, 0, .8);
    }
</style>
