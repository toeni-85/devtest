<template>
    <form @submit.prevent="submitForm">
        <Input type="text"
               id="name"
               placeholder="Name *"
               v-model="form.name"
               required
               class="mb-2" />

        <Input type="text"
               id="last_name"
               placeholder="Last Name *"
               v-model="form.last_name"
               class="mb-2"
               required />

        <Input type="email"
               id="email"
               placeholder="Email *"
               v-model="form.email"
               class="mb-2"
               ref="emailInput"
               required />

        <Input type="tel"
               id="phone"
               placeholder="Phone *"
               v-model="form.phone"
               class="mb-2"
               required />

        <p class="text-gray-500 text-xs italic mb-2">* Required field</p>

        <Button type="submit"
                variant="outline"
                :disabled="loading">
            {{ loading ? 'Saving...' : 'Save' }}
        </Button>
        
        <div v-if="showEmailErrorModal" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
            <p class="block sm:inline">This email has already been added to the shipment.</p>
        </div>
    </form>
</template>

<script setup lang="ts">
import axios from 'axios';
import { reactive, ref } from 'vue';
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'

const props = defineProps<{
    shipmentId: number | string;
    scope: string;
}>();

const emit = defineEmits<{
    'referent-added': [data: any];
    'error': [error: any];
}>();

const form = reactive({
    name: '',
    last_name: '',
    email: '',
    phone: '',
    scope: props.scope
});

interface ValidationErrors {
    [key: string]: string[]; // each field (email, name, phone…) can have multiple errors
}

const loading = ref(false);
const errors = ref<ValidationErrors>({});
const showEmailErrorModal = ref(false);

const submitForm = async () => {
    loading.value = true;
    errors.value = {};

    const emailTaken = await checkEmailExists(form.email);

    if (emailTaken) {
        showEmailErrorModal.value = true

        setTimeout(() => {
            showEmailErrorModal.value = false
        }, 5000)

        loading.value = false
        return
    }

    try {
        const response = await axios.post(
            `/shipments/${props.shipmentId}/addReferent`,
            form
        );

        emit('referent-added', response.data);
        resetForm();
        //reload page
        window.location.reload();

    } catch (error) {
        console.error('Error saving referent:', error);
        emit('error', error);
    } finally {
        loading.value = false;
    }
};

const checkEmailExists = async (email: string): Promise<boolean> => {
    if (!email) return false;

    try {
        const response = await axios.get(`/shipments/${props.shipmentId}/checkEmail`, {
            params: { email }
        });
        return response.data.exists;
    } catch (err) {
        console.error('Error checking email:', err);
        return false;
    }
};

const resetForm = () => {
    form.name = '';
    form.last_name = '';
    form.email = '';
    form.phone = '';
};
</script>