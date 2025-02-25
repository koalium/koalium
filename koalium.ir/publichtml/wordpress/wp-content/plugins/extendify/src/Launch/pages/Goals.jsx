import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import classNames from 'classnames';
import { CheckboxInputCard } from '@launch/components/CheckboxInputCard';
import { LoadingIndicator } from '@launch/components/LoadingIndicator';
import { Title } from '@launch/components/Title';
import { useGoals } from '@launch/hooks/useGoals';
import { PageLayout } from '@launch/layouts/PageLayout';
import { usePagesStore } from '@launch/state/Pages';
import { pageState } from '@launch/state/factory';
import { useUserSelectionStore } from '@launch/state/user-selections';
import * as IconComponents from '@launch/svg';

export const state = pageState('Goals', () => ({
	title: __('Goals', 'extendify-local'),
	ready: false,
	canSkip: false,
	useNav: true,
	onRemove: () => {},
}));

export const Goals = () => {
	const { loading, goals } = useGoals();

	return (
		<PageLayout>
			<div className="grow overflow-y-auto px-6 py-8 md:p-12 3xl:p-16">
				<Title
					title={__('What are your goals for your website?', 'extendify-local')}
					description={__(
						"We'll make sure your website has what it needs to achieve your goals.",
						'extendify-local',
					)}
				/>
				<div className="relative mx-auto w-full max-w-3xl">
					{loading ? <LoadingIndicator /> : <GoalsSelector goals={goals} />}
				</div>
			</div>
		</PageLayout>
	);
};

const GoalsSelector = ({ goals }) => {
	const { addMany, goals: selected } = useUserSelectionStore();
	const [selectedGoals, setSelectedGoals] = useState(selected ?? []);

	const nextPage = usePagesStore((state) => state.nextPage);

	useEffect(() => {
		state.setState({ ready: true });
	}, []);

	const handleGoalToggle = (goal) => {
		const alreadySelected = !!selectedGoals?.find(
			({ slug }) => slug === goal.slug,
		);
		const newSeletedGoals = alreadySelected
			? selectedGoals?.filter(({ slug }) => slug !== goal.slug)
			: [...selectedGoals, goal];
		setSelectedGoals(newSeletedGoals);
	};

	useEffect(() => {
		addMany('goals', selectedGoals, { clearExisting: true });
	}, [selectedGoals, addMany]);

	return (
		<form
			data-test="goals-form"
			onSubmit={(e) => {
				e.preventDefault();
				nextPage();
			}}
			className="goal-select grid w-full gap-4 xl:grid-cols-2">
			{/* Added so forms can be submitted by pressing Enter */}
			<input type="submit" className="hidden" />
			{goals?.map((goal, index) => {
				const selected = selectedGoals?.find(({ slug }) => slug === goal.slug);
				const Icon = IconComponents[goal.icon];
				return (
					<div
						key={goal.id}
						className={classNames(
							'relative rounded-lg border border-gray-300',
							{
								'bg-gray-100': selected,
							},
						)}
						data-test="goal-item">
						<div className="flex h-full items-center gap-4">
							<CheckboxInputCard
								autoFocus={index === 0}
								label={goal.title}
								id={`goal-${goal.slug}`}
								description={goal.description}
								checked={
									!!selectedGoals?.find(({ slug }) => slug === goal.slug)
								}
								onChange={() => handleGoalToggle(goal)}
								Icon={Icon}
							/>
						</div>
					</div>
				);
			})}
		</form>
	);
};
