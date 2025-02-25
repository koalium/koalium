import { useAIConsentStore } from '@shared/state/ai-consent';

export const AcceptTerms = () => {
	const { consentTermsHTML } = useAIConsentStore();

	return (
		<div className="flex flex-col">
			{consentTermsHTML ? (
				<p
					className="m-0 mt-6 p-0 text-sm text-gray-700"
					dangerouslySetInnerHTML={{ __html: consentTermsHTML }}
				/>
			) : null}
		</div>
	);
};
