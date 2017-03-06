<?php
namespace content\saloos_tg\sarshomarbot\commands\callback_query\help;
class faq_text{
	public static function text(){
		return array(
		array(
			"id" 		=> 1,
			"title" 	=> T_("What is Sarshomar?"),
			"text" 		=> [
			T_("Sarshomar is an integrative system to develop, manage and analyze online opinion polls in an efficient manner. Using Sarshomar is convenient, quick and without common complexities; its services are also available to the public."),
			T_("Using Sarshomar, do not be concerned about how to ask and conduct an analysis, and only concentrate on your question.")]
			),
		array(
			"id" 		=> 2,
			"title" 	=> T_("How does Sarshomar operate?"),
			"text" 		=> [
			T_("Sarshomar's main function is divided into two parts of questioning and answering."),
			T_("The enquired questions are private by default and for the spreading and receiving the viewpoints and opinions, the questioner will be responsible. When the questioner is in need of having access to statistical population, Sarshomar converts the question into a general one and inquires it of its valid population. In return, Sarshomar receives a charge from the questioner."),
			T_("In addition to the questions posed by the users, we will also pose ours to complete Sarshomar's rich knowledge."),
			T_("Having answered the questions, besides uploading your valuable comments and contributing to Sarshomar's valid findings, you will receive a portion of the payments provided by the questioners as well as be included in our lottery.")]
			),
		array(
			"id" 		=> 3,
			"title" 	=> T_("How can I upload my question?"),
			"text" 		=> T_("Click on the Upload a new question icon or on the /new icon to easily upload your question.")
			),
		array(
			"id" 		=> 4,
			"title" 	=> T_("How can I answer the other user's questions?"),
			"text" 		=> T_("In case you click on the Ask me icon or on the /Ask command, Sarshomar will start asking you questions.")
			),
		array(
			"id" 		=> 5,
			"title" 	=> T_("How can I complete my registration in Sarshomar?"),
			"text" 		=> [
			T_("Using Sarshomar Robot, due to its devoting a unique code to each user, is regarded as registration. However, to complete your registration and get it linked to Sarshomar After you cellphone number is uploaded, your account will be synchronized and it will be possible for you to log in the website.s website, after clicking on Dashboard icon or /dashboard command, select registration and synchronization option so that your cellphone number can be sent to the system."),
			T_("After you cellphone number is uploaded, your account will be synchronized and it will be possible for you to log in the website.")]
			),
		array(
			"id" 		=> 6,
			"title" 	=> T_("Are there any limitations to use Sarshomar?"),
			"text" 		=> [
			T_("The presupposition is that there are no limitations to use Sarshomar. However, to secure the quality of its services, and if special cases arise, Sarshomar is entitled to act upon the rules regarding the user accounts."),
			T_("We recommend that you read Sarshomar's terms and conditions of use.")]
			),
		array(
			"id" 		=> 7,
			"title" 	=> T_("Are there any limitations regarding the number of answers given to questions?"),
			"text" 		=> T_("No, there are no limitations for you to answer the questions.")
			),
		array(
			"id" 		=> 8,
			"title" 	=> T_("Can I use Sarshomar having several user accounts?"),
			"text" 		=> [
			T_("Sarshomar considers it as a violation and upon noticing it, Sarshomar is entitled to act upon the rules. Sarshomar's preventing users from having several user accounts is for the sake of the validity of the received answers, and to that end, Sarshomar has some strategies to recognize it."),
			T_("It is worth mentioning that the answers given by all the society's members, from all strata, is of great value for us and will contribute to more exact and valid results.")]
			),
		array(
			"id" 		=> 9,
			"title" 	=> T_("How does Sarshomar support its users?"),
			"text" 		=> T_("Sarshomar's support center is at your service and we will do our best to be accountable as well as solve potential problems of our respectful users.")
			),
		array(
			"id" 		=> 10,
			"title" 	=> T_("How can I delete my Sarshomar's user account?"),
			"text" 		=> [
			T_("The users of telegram robot who have not completed their registration, can turn to dashboard and select Disabling user account, to end their cooperation with Sarshomar."),
			T_("Those users who have confirmed their registration via uploading their cellphone number can turn to dashboard and select Delete user account to log in the website, and delete their user account after final confirmation.")]
			)
		);
	}
}
?>