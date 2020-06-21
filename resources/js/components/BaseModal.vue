<template>
    <div class="py-8 fixed inset-0 overflow-auto bg-black bg-opacity-50" v-show="show">
        <div class="ml-auto mr-auto bg-white rounded max-w-2xl shadow-xl">
            <div class="mb-4 pl-10 py-8 border-b text-left">
                <slot name="title" class="text-2xl font-semibold">
                    Default Modal Title - please use the "title" slot to override
                </slot>
            </div>

            <div class="px-10 py-8">
                <slot name="content">
                    Default Modal Content - please use the "content" slot to override
                </slot>
            </div>

            <div class="px-10 py-8 border-t">
                <slot name="bottom">
                    Default Modal Bottom Content - please use the "bottom" slot to override
                </slot>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        props: {
            show: {
                required: true,
            },

            scrollableBackground: {
                default: false,
            },
        },

        watch: {
            show: {
                immediate: true,
                handler: function (show) {
                    if (show) {
                        this.open();
                    } else {
                        this.close();
                    }
                },
            }
        },

        created() {
            const escapeHandler = (e) => {
                if (e.key === 'Escape' && this.show) {
                    this.close();
                }
            };

            document.addEventListener('keydown', escapeHandler);

            this.$once('hook:destroyed', () => {
                document.removeEventListener('keydown', escapeHandler);
            })
        },

        methods: {
            open() {
                !this.scrollableBackground && document.body.style.setProperty('overflow', 'hidden');
                this.$nextTick(() => {
                    this.$emit('open');
                });
            },

            close() {
                !this.scrollableBackground && document.body.style.removeProperty('overflow');
                this.$nextTick(() => {
                    this.$emit('close');
                });
            }
        }
    }
</script>

<style scoped>

</style>
