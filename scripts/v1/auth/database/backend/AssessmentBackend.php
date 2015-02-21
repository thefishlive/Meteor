<?php
namespace meteor\database\backend;

use common\data\Token;
use meteor\data\Assessment;
use meteor\data\profiles\AssessmentProfile;
use meteor\database\Backend;
use meteor\database\Database;
use meteor\exceptions\InvalidAssessmentException;

class AssessmentBackend {

    /** Assessment Operations */
    public static function fetch_all_assessments()
    {
        $query = Database::generate_query("assessment_lookup_all");
        $result = $query->execute();

        $assessments = array();
        while ($row = $result->fetch_data()) {
            $assessments[] = new AssessmentProfile(
                Token::decode($row["assessment_id"]),
                $row["assessment_name"],
                $row["assessment_display_name"]
            );
        }

        $result->close();

        return $assessments;
    }

    public static function fetch_assessment_profile(AssessmentProfile $profile)
    {
        $query = Database::generate_query("assessment_lookup_id", array($profile->getAssessmentId()->toString()));
        $result = $query->execute();

        if ($result->count() == 0) {
            throw new InvalidAssessmentException($profile);
        }

        $data = $result->fetch_data();
        $profile = new AssessmentProfile($profile->getAssessmentId(), $data['assessment_name'], $data['assessment_display_name']);

        $query = Database::generate_query("assessment_lookup_questions", array($profile->getAssessmentId()->toString()));
        $result = $query->execute();

        $questions = array();
        while ($row = $result->fetch_data()) {
            $json = json_decode($row["question_data"], true);
            $json["question-id"] = $row["question_id"];
            $questions[] = $json;
        }

        return new Assessment($profile, $questions);
    }

    public static function create_assessment(AssessmentProfile $profile, array $questions)
    {
        $query = Database::generate_query("assessment_create", array(
                $profile->getAssessmentId()->toString(),
                $profile->getName(),
                $profile->getDisplayName()
            ));
        $query->execute();

        foreach ($questions as $question) {
            $query = Database::generate_query("assessment_question_create", array(
                    $question["id"],
                    $profile->getAssessmentId()->toString(),
                    json_encode($question["data"])
                ));
            $query->execute();

            $query = Database::generate_query("assessment_answer_create", [
                    Token::generateNewToken(TOKEN_ANSWER)->toString(),
                    $question["id"],
                    $profile->getAssessmentId()->toString(),
                    (string) $question["data"]["answer"]
                ]);
            $query->execute();
        }

        return self::fetch_assessment_profile($profile);
    }

    public static function delete_assessment(AssessmentProfile $profile)
    {
        $query = Database::generate_query("assessment_delete", array($profile->getAssessmentId()->toString()));
        $query->execute();
    }
}