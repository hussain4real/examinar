import { ref } from 'vue';

const STORAGE_PREFIX = 'help-banner-dismissed:';

export function useHelpBanner(key: string) {
    const isVisible = ref(
        typeof window !== 'undefined'
            ? localStorage.getItem(`${STORAGE_PREFIX}${key}`) !== 'true'
            : true,
    );

    function dismiss() {
        isVisible.value = false;
        if (typeof window !== 'undefined') {
            localStorage.setItem(`${STORAGE_PREFIX}${key}`, 'true');
        }
    }

    return { isVisible, dismiss };
}
