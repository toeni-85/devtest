<template>

    <Head title="Shipments" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <h1 class="text-2xl font-semibold leading-tight">
                Shipment #{{ shipment.id }}:
                {{ shipment.from }} -> {{ shipment.to }}
            </h1>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h2 class="text-xl font-semibold mb-2">General Information</h2>
                    <p><strong>ID:</strong> {{ shipment.id }}</p>
                    <p><strong>TEAM:</strong> {{ shipment.team.name }}</p>
                    <p><strong>From:</strong> {{ shipment.from }}</p>
                    <p><strong>To:</strong> {{ shipment.to }}</p>
                </div>
            </div>

            <hr>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h2 class="text-xl font-semibold mb-2">Start Referents</h2>
                    <ul class="list-disc list-inside">
                        <li v-for="referent in shipment.referents.filter(
                            r => r.pivot.scope === 'start' && r.team_id === shipment.team.id
                        )" :key="referent.id">
                            {{ referent.name }} {{ referent.last_name }}
                            <br>
                            ({{ referent.email }})
                        </li>
                    </ul>
                </div>
                <div>
                    <ReferentForm
                        :shipment-id="shipment.id"
                        scope="start"
                        @referent-added="onReferentAdded"
                    />
                </div>
            </div>

            <hr>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h2 class="text-xl font-semibold mb-2">End Referents</h2>
                    <ul class="list-disc list-inside">
                        <li v-for="referent in shipment.referents.filter(
                            r => r.pivot.scope === 'end' && r.team_id === shipment.team.id
                        )" :key="referent.id">
                            {{ referent.name }} {{ referent.last_name }}
                            <br>
                            ({{ referent.email }})
                        </li>
                    </ul>
                </div>
                <div>
                    <ReferentForm
                        :shipment-id="shipment.id"
                        scope="end"
                        @referent-added="onReferentAdded"
                    />
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import ReferentForm from '@/components/ReferentForm.vue';

// Define and strongly type component props
const props = defineProps<{
    shipment: any;
}>();

// Create a local reactive copy that Vue CAN update
const shipment = ref(props.shipment);

// Handle Referent added event to update local shipment data
const onReferentAdded = ({ referent, scope }: any) => {
    shipment.value.referents.push({
        ...referent,
        pivot: { scope }
    });
};

console.log('Shipment props:', props.shipment);

// Define breadcrumbs for navigation
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Shipments',
        href: '/shipments',
    },
    {
        title: props.shipment.from + ' -> ' + props.shipment.to,
        href: '',
    }
];

</script>