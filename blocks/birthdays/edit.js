import { __ } from "@wordpress/i18n";
import { useSelect } from "@wordpress/data";
import { useBlockProps } from "@wordpress/block-editor";

const useBirthdaySelector = (type) => {
	return useSelect(
		(select) =>
			select("core")
				.getEntityRecords("postType", "fcmanager_" + type, {
					per_page: -1,
					meta_key: "_fcmanager_" + type + "_date_of_birth",
					meta_value: "today",
				})
				?.map((item) => ({
					id: item.id,
					name: item.title,
					age:
						item.meta["_fcmanager_" + type + "_publish_age"][0] === "true"
							? item.meta["_fcmanager_" + type + "_age"]
							: null,
				}))
		,
		[type],
	);
};

export default function Edit({ attributes, setAttributes }) {
	const players = useBirthdaySelector("player");
	const referees = useBirthdaySelector("referee");
	const volunteers = useBirthdaySelector("volunteer");
	const birthdays = useBirthdaySelector("birthday");

	const allBirthdays = [...(players || []), ...(referees || []), ...(volunteers || []), ...(birthdays || [])].sort(
		(a, b) => {
			const nameCompare = a.name.localeCompare(b.name);
			return nameCompare !== 0 ? nameCompare : a.age - b.age;
		});
	const allBirthdaysUnique = Array.from(
		new Map(
			allBirthdays.map(person => [
				person.name + '.' + person.age,
				person
			])
		).values()
	);

	return (
		<>
			<div {...useBlockProps()}>
				<div class="fcmanager-birthdays">
					<h2>{__("Birthdays", "football-club-manager")}</h2>
					{!allBirthdaysUnique?.length ? (
						<p>{__("No birthdays today.", "football-club-manager")}</p>
					) : (
						<ul class="fcmanager-people-name-list">
							{allBirthdaysUnique?.map((person) => (
								<li class="fcmanager-person-item" key={person.id}>
									{person.name} {person.age !== null ? ` (${person.age})` : ""}
								</li>
							))}
						</ul>
					)}
				</div>
			</div>
		</>
	);
}
