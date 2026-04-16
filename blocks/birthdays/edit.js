import { __ } from "@wordpress/i18n";
import { useSelect } from "@wordpress/data";
import { useBlockProps } from "@wordpress/block-editor";

export default function Edit({ attributes, setAttributes }) {
	const players = useSelect(
		(select) =>
			select("core")
				.getEntityRecords("postType", "fcmanager_player", {
					per_page: -1,
					meta_key: "_fcmanager_player_date_of_birth",
					meta_value: "today",
				})
				?.map((player) => ({
					id: player.id,
					name: player.title,
					age:
						player.meta._fcmanager_player_publish_age[0] === "true"
							? player.meta._fcmanager_player_age
							: null,
				})),
		[],
	);

	const volunteers = useSelect(
		(select) =>
			select("core")
				.getEntityRecords("postType", "fcmanager_volunteer", {
					per_page: -1,
					meta_key: "_fcmanager_volunteer_date_of_birth",
					meta_value: "today",
				})
				?.map((volunteer) => ({
					id: volunteer.id,
					name: volunteer.title,
					age:
						volunteer.meta._fcmanager_volunteer_publish_age[0] === "true"
							? volunteer.meta._fcmanager_volunteer_age
							: null,
				})),
		[],
	);

	const birthdays = useSelect(
		(select) =>
			select("core")
				.getEntityRecords("postType", "fcmanager_birthday", {
					per_page: -1,
					meta_key: "_fcmanager_birthday_date_of_birth",
					meta_value: "today",
				})
				?.map((birthday) => ({
					id: birthday.id,
					name: birthday.title,
					age:
						birthday.meta._fcmanager_birthday_publish_age[0] === "true"
							? birthday.meta._fcmanager_birthday_age
							: null,
				})),
		[],
	);

	const allBirthdays = [...(players || []), ...(volunteers || []), ...(birthdays || [])].sort(
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
