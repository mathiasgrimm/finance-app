<template>
    <base-modal
        :show="localShow"
        @open="open"
        @close="close"
    >
        <template slot="title">
            <h1 class="text-2xl font-semibold">Add Balance Entry</h1>
        </template>

        <template slot="content">
            <form>
                <div class="flex">
                    <div class="w-full md:w-4/12 text-left">
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

                    <div class="w-full md:w-5/12 ml-3 text-left">
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

                    <div class="w-full md:w-3/12 ml-3 text-left">
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
            </form>
        </template>

        <template slot="bottom">
            <div class=" flex justify-end items-center">
                <button
                    class="py-5 px-8 rounded items-center text-xs font-bold tracking-wider bg-gray-300 hover:bg-gray-400 text-gray-600"
                    @click.prevent="close"
                >CANCEL
                </button>

                <button
                    class="py-5 px-8 ml-4 rounded items-center text-xs font-bold tracking-wider bg-primary hover:bg-blue-900 text-white"
                    @click.prevent="addTransaction"
                >SAVE ENTRY
                </button>
            </div>
        </template>

    </base-modal>
</template>

<script>
    import BaseModal from "./BaseModal";
    export default {
        data() {
            return {
                localShow: false,
                form: {
                    label: null,
                    transaction_at: null,
                    amount: null,
                }
            }
        },

        components: {
            BaseModal,
        },

        props: {
            show: {
                required: true,
            },

            userId: {
                required: true,
            }
        },

        beforeMount() {
            this.localShow = this.show;
        },

        methods: {
            open() {
                this.reset();
                this.localShow = true;
                this.$nextTick(() => {
                    this.$emit('open');
                });
            },

            close() {
                this.localShow = false;
                this.$nextTick(() => {
                    this.$emit('close');
                });
            },

            reset() {
                this.form = {
                    label: null,
                    date: null,
                    amount: null,
                }
            },

            addTransaction() {
                axios.post(`/users/${this.userId}/transactions`, this.form).then(res => {
                    this.close();
                    this.$nextTick(() => {
                        this.$emit('success');
                    })
                }).catch(err => {
                    // TODO
                    alert('Something went wrong');
                })
            }
        }
    }
</script>
