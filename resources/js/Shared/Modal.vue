<template>
    <div v-if="showing" class="fixed inset-0 w-full h-screen flex items-center justify-center overflow-auto">
        <div class="w-full max-w-6xl bg-white shadow-lg rounded-lg p-8">
            <h3>
                <slot name="title"></slot>
            </h3>
            <slot></slot>
            <button @click="close" class="button rounded">close modal</button>
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
