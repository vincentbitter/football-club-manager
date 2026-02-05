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

	return (
		<>
			<div {...useBlockProps()}>
				<div class="fcmanager-birthdays">
					<h2>{__("Birthdays", "football-club-manager")}</h2>
					{!players?.length ? (
						<p>{__("No birthdays today.", "football-club-manager")}</p>
					) : (
						<ul class="fcmanager-player-name-list">
							{players?.map((player) => (
								<li class="fcmanager-player-item" key={player.id}>
									{player.name} {player.age !== null ? ` (${player.age})` : ""}
								</li>
							))}
						</ul>
					)}
				</div>
			</div>
		</>
	);
}
