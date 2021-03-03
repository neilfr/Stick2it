<template>
<div>
    <h1>Create Log Entry</h1>
    <form method="POST" @submit.prevent="submit">
        <div class="grid grid-cols-2 gap-2">
            <p class="col-span-2" v-if="errors.quantity">{{errors.quantity}}</p>
            <label class="p-2" for="quantity">Quantity:</label>
            <input class="border rounded" id="quantity" type="number" min="0" v-model="quantity">
            <p class="col-span-2" v-if="errors.consumed_at">{{errors.consumed_at}}</p>
            <label class="p-2" for="consumed_at">Consumed at:</label>
            <input class="border rounded" id="consumed_at" type="date" :value="consumed_at">
        </div>
    </form>
</div>
</template>

<script>
export default {
    mounted(){
        this.mydate = new Date();
        this.foo.setDate(this.foo.getDate(new Date())+10);
        console.log(this.foo.toISOString().substr(0,10));
        this.consumed_at=this.foo.toISOString().substr(0,10);
    },
    props: {
        errors: Object,
    },
    data() {
        return {
            foo: 0,
            food: 'foo',
            consumed_at: '2021-02-01',
            quantity: 99
        }
    }
}

</script>
