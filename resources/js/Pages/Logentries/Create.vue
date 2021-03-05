<template>
    <div>
        <h1>Create Log Entry</h1>
        <form method="POST" @submit.prevent="submit">
            <div class="grid grid-cols-2 gap-2">
                <p class="col-span-2" v-if="errors.quantity">{{errors.quantity}}</p>
                <label class="p-2" for="quantity">Quantity:</label>
                <input class="border rounded" id="quantity" type="number" min="0" v-model="logentry.quantity">
                <p class="col-span-2" v-if="errors.consumed_at">{{errors.consumed_at}}</p>
                <label class="p-2" for="consumed_at">Consumed at:</label>
                <input class="border rounded" id="consumed_at_date" type="date" v-model="logentry.consumed_at">
            </div>
        </form>
        <button @click="store">Save</button>
        <button>Cancel</button>
    </div>
</template>

<script>
export default {
    props: {
        errors: Object,
        user: Object
    },
    data() {
        return {
            logentry: {
                user_id: this.user.id,
                food_id: 2,
                consumed_at: (new Date()).toISOString().substr(0,10),
                quantity: 0
            }
        }
    },
    methods: {
        store(){
            this.$inertia.post(
                this.$route("logentries.store"), this.logentry
            ).then(()=>{
                console.log("errors", this.errors.description);
            });
        }
    }
}
</script>
