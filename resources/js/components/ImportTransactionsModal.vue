<template>
    <base-modal
        :show="localShow"
        @open="open"
        @close="close"
    >
        <template slot="title">
            <h1 class="text-2xl font-semibold">Import Balance Entries</h1>
        </template>

        <template slot="content">
            <form>
                <div>
                    <label class="cursor-pointer">
                        <span class="block text-gray-700 text-xs font-bold mb-2">.CSV FILE</span>

                        <input
                            class="hidden"
                            type="file"
                            ref="file"
                            accept=".csv"
                            @change="fileChanged"
                        >

                        <div
                            class="flex justify-between items-center py-3 px-4 mb-3 text-sm text-gray-700 border border-gray-300 rounded"
                        >
                            <div class="text-sm">
                                {{ fileName }}
                            </div>

                            <div class="text-primary underline font-bold text-sm">
                                Select File
                            </div>
                        </div>

                    </label>

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
                    class="py-5 px-12 ml-4 rounded items-center text-xs font-bold tracking-wider bg-primary hover:bg-blue-900 text-white"
                    @click.prevent="handleFileUpload"
                >IMPORT
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
                fileName: null,
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
                this.fileName = null;
                this.form = {
                    label: null,
                    date: null,
                    amount: null,
                }
            },

            fileChanged() {
                this.fileName = this.$refs.file.files[0].name;
            },

            handleFileUpload() {
                const formData = new FormData();
                formData.append('transactions', this.$refs.file.files[0]);

                axios.post(`/users/${this.userId}/transaction-imports`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(res => {
                    this.close();

                    this.$nextTick(() => {
                        this.$emit('importing', res.data.data.records);
                    });
                }).catch(err => {
                    // TODO
                    alert('something went wrong');
                });
            }
        }
    }
</script>
