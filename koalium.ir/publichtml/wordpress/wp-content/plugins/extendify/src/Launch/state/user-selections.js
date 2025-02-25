import apiFetch from '@wordpress/api-fetch';
import { safeParseJson } from '@shared/lib/parsing';
import { create } from 'zustand';
import { devtools, persist, createJSONStorage } from 'zustand/middleware';

const initialState = {
	siteType: {
		slug: '0default',
		name: 'Default',
	},
	siteStructure: undefined,
	siteProfile: undefined,
	siteStrings: undefined,
	siteImages: undefined,
	siteInformation: {
		title: window.extSharedData.siteTitle || '',
	},
	businessInformation: {
		description: undefined,
		tones: [],
		acceptTerms: false,
	},
	goals: [],
};

// State to reset when the business description (site type) changes
const resetState = {
	siteStructure: undefined,
	siteProfile: undefined,
	siteStrings: undefined,
	siteImages: undefined,
	goals: [],
	variation: null,
};

const key = `extendify-launch-user-selection-${window.extSharedData.siteId}`;
const incoming = safeParseJson(window.extSharedData.userData.userSelectionData);
const state = (set, get) => ({
	...initialState,
	// initialize the state with default values
	...(incoming?.state ?? {}),
	...(JSON.parse(localStorage.getItem(key) || '{}')?.state ?? {}), // For testing
	setSiteStructure(siteStructure) {
		if (!['single-page', 'multi-page'].includes(siteStructure)) {
			throw new Error("Page structure doesn't exist");
		}
		set({ siteStructure });
	},
	setSiteInformation(name, value) {
		const siteInformation = { ...get().siteInformation, [name]: value };
		set({ siteInformation });
	},
	setBusinessInformation(name, value) {
		const businessInformation = { ...get().businessInformation, [name]: value };
		set({ businessInformation });
	},
	setSiteProfile(data) {
		set(resetState);
		if (!data) data = {};
		const siteProfile = Object.assign(
			{
				aiSiteType: null,
				aiSiteCategory: null,
				aiDescription: null,
				aiKeywords: [],
			},
			data,
		);
		set({ siteProfile });
	},
	setSiteStrings: (data) => {
		if (!data) data = {};
		const siteStrings = Object.assign(
			{ aiHeaders: [], aiBlogTitles: [] },
			data,
		);
		set({ siteStrings });
	},
	setSiteImages: (data) => {
		if (!data) data = {};
		const siteImages = Object.assign({ siteImages: [] }, data);
		set({ siteImages });
	},
	getGoalsPlugins() {
		return get().goals.flatMap((goal) => goal.plugins);
	},
	has(type, item) {
		if (!item?.id) return false;
		return (get()?.[type] ?? [])?.some((t) => t.id === item.id);
	},
	add(type, item) {
		if (get().has(type, item)) return;
		set({ [type]: [...(get()?.[type] ?? []), item] });
	},
	addMany(type, items, options = {}) {
		if (options.clearExisting) {
			set({ [type]: items });
			return;
		}
		set({ [type]: [...(get()?.[type] ?? []), ...items] });
	},
	remove(type, item) {
		set({ [type]: get()?.[type]?.filter((t) => t.id !== item.id) });
	},
	removeMany(type, items) {
		set({
			[type]: get()?.[type]?.filter((t) => !items.some((i) => i.id === t.id)),
		});
	},
	removeAll(type) {
		set({ [type]: [] });
	},
	toggle(type, item) {
		if (get().has(type, item)) {
			get().remove(type, item);
			return;
		}
		get().add(type, item);
	},
	resetState() {
		set(initialState);
	},
	setVariation(variation) {
		set({ variation });
	},
});

const path = '/extendify/v1/shared/user-selections-data';
const storage = {
	getItem: async () => await apiFetch({ path }),
	setItem: async (_name, state) =>
		await apiFetch({ path, method: 'POST', data: { state } }),
};

export const useUserSelectionStore = create(
	persist(devtools(state, { name: 'Extendify User Selections' }), {
		storage: createJSONStorage(() => storage),
		skipHydration: true,
	}),
	state,
);
