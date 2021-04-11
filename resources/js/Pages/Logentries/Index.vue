<template>
    <div class="container">
        <div>
            <label for="from">From:</label>
            <input type="date" name="from" id="from" v-model='from' @change="refresh">
        </div>
        <div>
            <label for="to">To:</label>
            <input type="date" name="to" id="to" v-model='to' @change="refresh">
        </div>
        <div class='flex justify-between'>
            <h1>Log Entries</h1>
            <button @click="add">Add Log Entry</button>
        </div>
        <table>
            <tr>
                <th>Date / Time</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>KCal</th>
                <th>Fat</th>
                <th>Protein</th>
                <th>Carbohydrate</th>
                <th>Potassium</th>
                <th>Actions</th>
            </tr>
            <tr v-for="logentry in logentries.data" :key="logentry.id">
                <td>{{logentry.consumed_at}}</td>
                <td>{{logentry.description}}</td>
                <td>{{logentry.quantity}}</td>
                <td>{{logentry.kcal}}</td>
                <td>{{logentry.fat}}</td>
                <td>{{logentry.protein}}</td>
                <td>{{logentry.carbohydrate}}</td>
                <td>{{logentry.potassium}}</td>
                <td><button @click="edit(logentry)">Edit</button><button @click="destroy(logentry)">Delete</button></td>
            </tr>
        </table>
    </div>
</template>

<script>
export default {
    props:{
        logentries: Object,
    },
    data(){
        return {
            today: '',
            from: '',
            to: '',
            myhello:''
        }
    },
    mounted(){
        this.today = new Date()
        this.to = this.today.toISOString().substr(0,10);

        this.lastWeek = new Date();
        this.lastWeek.setDate(this.lastWeek.getDate()-7);
        this.from = this.lastWeek.toISOString().substr(0,10);
    },
    methods:{
        add () {
            let url = `${this.$route("logentries.create")}`;
            console.log(url);
            this.$inertia.visit(url);
        },
        edit(logentry) {
            let url = `${this.$route("logentries.edit", logentry)}`;
            this.$inertia.visit(url);
        },
        destroy(logentry) {
            let url = this.$route("logentries.destroy", logentry.id);
            this.$inertia.delete(url);
        },
        refresh(){
            let url = `${this.$route("logentries.index")}`;
            url += `?from=${this.from}`;
            url += `&to=${this.to}`;
            this.$inertia.visit(url);
        }
    }
}
</script>
