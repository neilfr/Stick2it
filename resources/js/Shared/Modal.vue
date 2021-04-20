<template>
    <div v-if="showing" class="fixed inset-0 w-full h-screen flex items-center justify-center overflow-auto">
        <div class="w-full max-w-6xl bg-white shadow-lg rounded-lg p-8">
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-2">
                    <slot name="title"></slot>
                </h2>
                <button @click="close">
                    <img class="w-6" src="/images/close.svg">
                </button>
            </div>
            <slot></slot>
        </div>
    </div>
</template>

<script>
export default {
    props:{
        showing: {
            required:true,
            type:Boolean,
        }
    },
    methods:{
        close () {
            this.$emit("close");
        }
    }
}
</script>
